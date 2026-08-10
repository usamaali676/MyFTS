<?php

namespace App\Services;

use App\Exceptions\TranscriptionFailedException;
use App\Models\CallTranscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class CallTranscriptionService
{
    // whisper-1, not the newer gpt-4o(-mini)-transcribe models: those are
    // generative and have their own output-token ceiling with no internal
    // audio chunking, so they silently truncate long calls partway through
    // instead of transcribing the full file. whisper-1 is purpose-built for
    // full-length audio and doesn't have that cutoff.
    private const TRANSCRIBE_MODEL = 'whisper-1';
    private const DIARIZE_MODEL = 'gpt-4o-mini';
    private const MAX_RETRIES = 2;
    private const LABEL_CHUNK_SIZE = 25;

    private const REV_AI_BASE_URL = 'https://api.rev.ai/speechtotext/v1';
    private const REV_AI_POLL_INTERVAL_SECONDS = 5;
    // Safety ceiling, not the expected time -- a 17-minute benchmark call
    // transcribed in ~100s in testing. This just bounds how long a stuck
    // Rev AI job can hold up the queue job before falling back to OpenAI.
    private const REV_AI_MAX_WAIT_SECONDS = 1800;

    public function __construct(
        private readonly TranscriptComplianceService $complianceService,
    ) {}

    /**
     * Creates the record, transcribes/diarizes the recording, and returns it
     * with a final status of 'completed' or 'failed'. Runs synchronously in
     * the request: Rev AI (real acoustic diarization) typically finishes a
     * call in one to two minutes, which is well within a normal HTTP request,
     * so there's no need for a queued job and the polling/worker-process
     * machinery that comes with one.
     */
    public function generate(UploadedFile $file, User $agent, string $requestUuid, int $createdByUserId): CallTranscription
    {
        $existing = CallTranscription::where('uuid', $requestUuid)
            ->whereIn('status', ['processing', 'completed'])
            ->first();

        if ($existing) {
            return $existing;
        }

        $record = CallTranscription::create([
            'uuid' => $requestUuid,
            'agent_user_id' => $agent->id,
            'agent_name_snapshot' => $agent->name,
            'original_filename' => $file->getClientOriginalName(),
            'file_size_bytes' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'processing',
            'created_by' => $createdByUserId,
        ]);

        // Move the upload to a stable local path. The PHP temp-upload file behind
        // $file->getRealPath() has no file extension, which the Whisper API needs
        // to detect the audio format.
        $extension = $file->getClientOriginalExtension() ?: 'mp3';
        $storedDir = storage_path('app/tmp/call-transcriptions');
        if (!is_dir($storedDir)) {
            mkdir($storedDir, 0755, true);
        }
        $storedName = $requestUuid . '.' . $extension;
        $file->move($storedDir, $storedName);
        $storedPath = $storedDir . DIRECTORY_SEPARATOR . $storedName;

        $this->process($record, $storedPath, $agent->name);

        return $record->fresh();
    }

    /**
     * The actual transcription/diarization work. Tries Rev AI (real acoustic
     * diarization) first; if it's unreachable, times out, or returns something
     * malformed, falls back to the OpenAI-only pipeline so a call still gets
     * transcribed either way.
     */
    private function process(CallTranscription $record, string $storedPath, string $agentName): void
    {
        $startedAt = microtime(true);

        try {
            $viaRevAi = $this->transcribeViaRevAi($storedPath);

            if ($viaRevAi !== null) {
                [$rawTurns, $durationSeconds] = $viaRevAi;
            } else {
                [$segments, $durationSeconds] = $this->transcribe($storedPath);
                $rawTurns = $this->diarize($segments, $agentName);
            }

            $durationSeconds ??= $this->readDuration($storedPath);
            $turns = $this->finalizeTurns($rawTurns, $agentName);

            $record->update([
                'status' => 'completed',
                'transcript_json' => $turns,
                'duration_seconds' => $durationSeconds,
                'word_count' => $this->countWords($turns),
                'exchange_count' => count($turns),
                'processing_time_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);

            // The transcript itself is already saved and successful at this point.
            // TranscriptComplianceService::analyze() catches its own OpenAI/JSON
            // errors internally and records them on the model rather than
            // throwing, but it's wrapped here too so any truly unexpected failure
            // in the compliance pass still can't roll a completed transcription
            // back into a "failed" one.
            try {
                $this->complianceService->analyze($record);
            } catch (\Throwable $e) {
                Log::error('Compliance analysis threw unexpectedly after transcription succeeded', [
                    'call_transcription_id' => $record->id,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ]);
            }
        } catch (TranscriptionFailedException $e) {
            $record->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Call transcription failed', [
                'call_transcription_id' => $record->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            $record->update(['status' => 'failed', 'error_message' => 'An unexpected error occurred.']);
        } finally {
            if (is_file($storedPath)) {
                @unlink($storedPath);
            }
        }
    }

    /**
     * Calls Rev AI's async speech-to-text API (real acoustic diarization).
     * Returns null on any failure -- not configured, submission rejected,
     * job failed/timed out, or a malformed transcript -- so process() can
     * fall back to the OpenAI pipeline instead of failing the transcription
     * outright. Never throws.
     *
     * @return array{0: array<int, array{speaker: string, text: string, start: int}>, 1: ?int}|null
     */
    private function transcribeViaRevAi(string $path): ?array
    {
        $token = config('services.revai.token');

        if (!$token) {
            return null;
        }

        try {
            $submission = Http::timeout(120)
                ->connectTimeout(10)
                ->withToken($token)
                ->attach('media', fopen($path, 'r'), basename($path))
                ->post(self::REV_AI_BASE_URL . '/jobs');

            if (!$submission->successful()) {
                Log::warning('Rev AI job submission failed; falling back to OpenAI', [
                    'status' => $submission->status(),
                ]);

                return null;
            }

            $jobId = $submission->json('id');

            if (!$jobId) {
                Log::warning('Rev AI job submission returned no job id; falling back to OpenAI');

                return null;
            }

            $durationSeconds = null;
            $waited = 0;

            while (true) {
                sleep(self::REV_AI_POLL_INTERVAL_SECONDS);
                $waited += self::REV_AI_POLL_INTERVAL_SECONDS;

                $jobStatus = Http::timeout(30)->withToken($token)
                    ->get(self::REV_AI_BASE_URL . '/jobs/' . $jobId);

                if (!$jobStatus->successful()) {
                    Log::warning('Rev AI job status check failed; falling back to OpenAI', [
                        'status' => $jobStatus->status(),
                    ]);

                    return null;
                }

                $status = $jobStatus->json('status');

                if ($status === 'transcribed') {
                    $durationSeconds = $jobStatus->json('duration_seconds');

                    break;
                }

                if ($status === 'failed') {
                    Log::warning('Rev AI job failed; falling back to OpenAI', [
                        'failure_detail' => $jobStatus->json('failure_detail'),
                    ]);

                    return null;
                }

                if ($waited >= self::REV_AI_MAX_WAIT_SECONDS) {
                    Log::warning('Rev AI job did not finish within the wait ceiling; falling back to OpenAI', [
                        'waited_seconds' => $waited,
                    ]);

                    return null;
                }
            }

            $transcript = Http::timeout(60)
                ->withToken($token)
                ->withHeaders(['Accept' => 'application/vnd.rev.transcript.v1.0+json'])
                ->get(self::REV_AI_BASE_URL . '/jobs/' . $jobId . '/transcript');

            if (!$transcript->successful()) {
                Log::warning('Rev AI transcript retrieval failed; falling back to OpenAI', [
                    'status' => $transcript->status(),
                ]);

                return null;
            }

            $monologues = $transcript->json('monologues');

            if (!is_array($monologues) || count($monologues) === 0) {
                Log::warning('Rev AI returned no monologues; falling back to OpenAI');

                return null;
            }

            $rawTurns = [];
            $wordCountsBySpeaker = [];

            foreach ($monologues as $monologue) {
                $speaker = $monologue['speaker'] ?? null;
                $elements = $monologue['elements'] ?? null;

                if ($speaker === null || !is_array($elements)) {
                    continue;
                }

                $text = '';
                $start = null;

                foreach ($elements as $element) {
                    $type = $element['type'] ?? null;

                    if ($type === 'text' || $type === 'punct') {
                        $text .= $element['value'] ?? '';
                    }

                    if ($type === 'text' && $start === null) {
                        $start = $element['ts'] ?? null;
                    }
                }

                $text = trim($text);

                if ($text === '') {
                    continue;
                }

                $rawTurns[] = ['speaker' => $speaker, 'text' => $text, 'start' => (int) round($start ?? 0)];
                $wordCountsBySpeaker[$speaker] = ($wordCountsBySpeaker[$speaker] ?? 0) + str_word_count($text);
            }

            if (count($rawTurns) === 0) {
                Log::warning('Rev AI transcript had no usable text; falling back to OpenAI');

                return null;
            }

            // Rev AI numbers speakers anonymously (0, 1, ...) -- it doesn't know
            // which one is the agent. Sales calls are agent-monologue-heavy, so
            // whichever speaker has the most total words is far more reliable
            // than assuming whoever speaks first is the agent (wrong whenever
            // the client opens the call with small talk, as confirmed on our
            // benchmark call).
            arsort($wordCountsBySpeaker);
            $agentSpeaker = array_key_first($wordCountsBySpeaker);

            foreach ($rawTurns as &$turn) {
                $turn['speaker'] = $turn['speaker'] === $agentSpeaker ? 'agent' : 'client';
            }
            unset($turn);

            return [$rawTurns, $durationSeconds !== null ? (int) round($durationSeconds) : null];
        } catch (\Throwable $e) {
            Log::warning('Rev AI call failed; falling back to OpenAI', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function readDuration(string $path): ?int
    {
        try {
            $getID3 = new \getID3();
            $info = $getID3->analyze($path);
            $seconds = $info['playtime_seconds'] ?? null;

            return $seconds !== null ? (int) round($seconds) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array{0: array<int, array{text: string, start: int}>, 1: ?int}
     */
    private function transcribe(string $path): array
    {
        $attempt = 0;

        while (true) {
            try {
                // verbose_json, not plain json: it returns real per-segment start
                // timestamps from the audio itself, which we use directly instead
                // of estimating turn timing from word-count proportions. It also
                // sidesteps a separate bug -- whisper-1's plain-json output can
                // come back with no punctuation at all on lower-quality phone
                // audio, which broke sentence-boundary splitting entirely.
                // Segments are natural pause-bounded phrases regardless of
                // punctuation, so they work as diarization units either way.
                $response = OpenAI::audio()->transcribe([
                    'model' => self::TRANSCRIBE_MODEL,
                    'file' => fopen($path, 'r'),
                    'response_format' => 'verbose_json',
                ]);

                $segments = [];
                foreach ($response->segments as $segment) {
                    $text = trim($segment->text);

                    if ($text !== '') {
                        $segments[] = ['text' => $text, 'start' => (int) round($segment->start)];
                    }
                }

                if (count($segments) === 0) {
                    throw new TranscriptionFailedException('No speech was detected in the uploaded recording.', 422);
                }

                $duration = $response->duration !== null ? (int) round($response->duration) : null;

                return [$segments, $duration];
            } catch (TranscriptionFailedException $e) {
                throw $e;
            } catch (\OpenAI\Exceptions\RateLimitException|\OpenAI\Exceptions\ServerException|\OpenAI\Exceptions\TransporterException $e) {
                if (++$attempt > self::MAX_RETRIES) {
                    Log::error('OpenAI call failed after max retries', [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'attempts' => $attempt,
                    ]);
                    throw new TranscriptionFailedException('The transcription service is temporarily unavailable. Please try again shortly.', 503);
                }
                usleep(300000 * $attempt);
            }
            catch (\OpenAI\Exceptions\ErrorException $e) {
                throw new TranscriptionFailedException('The transcription service rejected the audio file: ' . $e->getMessage(), 422);
            }
        }
    }

    /**
     * @param  array<int, array{text: string, start: int}>  $segments
     * @return array<int, array{speaker: string, text: string, start: int}>
     */
    private function diarize(array $segments, string $agentName): array
    {
        // Ask the model to label EVERY segment individually (not choose its own
        // grouping), in small chunks. A single request covering a whole long
        // call (100+ segments) reliably hits the model's output token cap
        // mid-array (finish_reason "length"), producing truncated/invalid JSON
        // that either fails outright or falls back to padding mismatched labels
        // with a repeat of the last one -- collapsing most of the call into 1-2
        // giant blocks. Small chunks keep each response tiny enough to never
        // truncate, and confine any single request's mistakes to ~25 segments
        // instead of the whole call.
        if (count($segments) === 0) {
            throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
        }

        $lines = array_column($segments, 'text');
        $starts = array_column($segments, 'start');

        $labels = $this->labelLines($lines, $agentName);

        return $this->groupLabeledLines($lines, $starts, $labels);
    }

    /**
     * @param  array<int, string>  $lines
     * @return array<int, string> one 'agent'|'client' label per line, same order/length as $lines
     */
    private function labelLines(array $lines, string $agentName): array
    {
        $labels = [];
        $previousSpeaker = null;
        $previousLineText = null;

        foreach (array_chunk($lines, self::LABEL_CHUNK_SIZE) as $chunk) {
            $chunkLabels = $this->labelChunk($chunk, $agentName, $previousSpeaker, $previousLineText);
            $labels = array_merge($labels, $chunkLabels);

            $previousSpeaker = $chunkLabels[count($chunkLabels) - 1] ?? $previousSpeaker;
            $previousLineText = $chunk[count($chunk) - 1] ?? $previousLineText;
        }

        return $labels;
    }

    /**
     * @param  array<int, string>  $lines
     * @return array<int, string> one 'agent'|'client' label per line, same order/length as $lines
     */
    private function labelChunk(array $lines, string $agentName, ?string $previousSpeaker, ?string $previousLineText): array
    {
        $count = count($lines);
        $numbered = implode("\n", array_map(
            static fn (int $i, string $line): string => ($i + 1) . ': ' . $line,
            array_keys($lines),
            $lines,
        ));

        $context = $previousSpeaker !== null
            ? "For continuity only (this line is from just before this excerpt and is already labeled -- do NOT include it in your output): line 0 was spoken by \"{$previousSpeaker}\": \"{$previousLineText}\". Keep applying that same speaker identity consistently; do not swap which name means agent vs client partway through.\n\n"
            : '';

        $basePrompt = <<<PROMPT
            You are given a phone-call transcript mechanically split into {$count} numbered text fragments
            by PAUSES IN THE RAW AUDIO -- not by who is speaking. Many consecutive fragments are one
            uninterrupted sentence or monologue from the SAME person, cut apart only because of a breath,
            filler word, or the transcription engine's own chunking. A fragment that ends mid-word,
            mid-thought, or without a natural sentence ending is almost always continued by the very next
            fragment from the SAME speaker.

            Two people are talking:
            - "{$agentName}" (the agent / call center representative)
            - the caller (their customer)

            For each fragment, decide who is speaking. Default to keeping the SAME speaker as the previous
            fragment. Only switch speakers when the content clearly signals a genuine handoff -- e.g. a
            direct reply or acknowledgment to what was just said, a shift from the agent's pitch to the
            client asking or answering something about their own account, or a statement that only makes
            sense as a response to the fragment before it. Do not alternate speakers just because a
            fragment boundary exists.

            {$context}Return strict JSON only, no prose, in this exact shape — exactly {$count} labels, one per
            line, in the same order as the transcript:
            {"labels": ["agent", "client", "client", "agent"]}

            Numbered transcript:
            {$numbered}
            PROMPT;

        $attempt = 0;
        $strict = false;

        while (true) {
            try {
                $prompt = $strict
                    ? $basePrompt . "\n\nYour previous response did not contain exactly {$count} labels. Count carefully and return exactly {$count} entries."
                    : $basePrompt;

                $response = OpenAI::chat()->create([
                    'model' => self::DIARIZE_MODEL,
                    'temperature' => 0,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => 'You label phone-call transcript lines by speaker and respond with strict JSON only.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

                $decoded = json_decode($response->choices[0]->message->content, true);
                $labels = $decoded['labels'] ?? null;

                if (!is_array($labels) || count($labels) === 0) {
                    throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
                }

                $normalized = array_map(
                    static fn ($label): string => $label === 'agent' ? 'agent' : 'client',
                    array_values($labels),
                );

                if (count($normalized) === $count) {
                    return $normalized;
                }

                // Count mismatch: retry once with a stricter reminder, then fall
                // back to padding/truncating rather than failing the whole
                // (already paid-for) transcription outright.
                if (!$strict && $attempt < self::MAX_RETRIES) {
                    $strict = true;
                    $attempt++;
                    continue;
                }

                return $this->reconcileLabelCount($normalized, $count);
            } catch (TranscriptionFailedException $e) {
                throw $e;
            } catch (\OpenAI\Exceptions\RateLimitException|\OpenAI\Exceptions\ServerException|\OpenAI\Exceptions\TransporterException $e) {
                if (++$attempt > self::MAX_RETRIES) {
                    Log::error('OpenAI call failed after max retries', [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'attempts' => $attempt,
                    ]);
                    throw new TranscriptionFailedException('The transcription service is temporarily unavailable. Please try again shortly.', 503);
                }
                usleep(300000 * $attempt);
            } catch (\OpenAI\Exceptions\ErrorException $e) {
                throw new TranscriptionFailedException('The transcription service encountered an error while analyzing speakers.', 422);
            }
        }
    }

    /**
     * @param  array<int, string>  $labels
     * @return array<int, string>
     */
    private function reconcileLabelCount(array $labels, int $targetCount): array
    {
        if (count($labels) > $targetCount) {
            return array_slice($labels, 0, $targetCount);
        }

        $last = $labels[count($labels) - 1] ?? 'client';
        while (count($labels) < $targetCount) {
            $labels[] = $last;
        }

        return $labels;
    }

    /**
     * @param  array<int, string>  $lines
     * @param  array<int, int>  $starts
     * @param  array<int, string>  $labels
     * @return array<int, array{speaker: string, text: string, start: int}>
     */
    private function groupLabeledLines(array $lines, array $starts, array $labels): array
    {
        $turns = [];
        $currentSpeaker = null;
        $turnStart = null;
        $buffer = [];

        foreach ($lines as $i => $line) {
            $speaker = $labels[$i] ?? ($currentSpeaker ?? 'client');

            if ($currentSpeaker !== null && $speaker !== $currentSpeaker) {
                $turns[] = ['speaker' => $currentSpeaker, 'text' => trim(implode(' ', $buffer)), 'start' => $turnStart];
                $buffer = [];
            }

            if ($buffer === []) {
                $turnStart = $starts[$i] ?? $turnStart ?? 0;
            }

            $currentSpeaker = $speaker;
            $buffer[] = $line;
        }

        if ($buffer !== []) {
            $turns[] = ['speaker' => $currentSpeaker, 'text' => trim(implode(' ', $buffer)), 'start' => $turnStart];
        }

        if (count($turns) === 0) {
            throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
        }

        return $turns;
    }

    /**
     * @param  array<int, array{speaker: string, text: string, start: int}>  $turns
     * @return array<int, array{speaker: string, speaker_label: string, text: string, timestamp_seconds: ?int, timestamp_label: ?string}>
     */
    private function finalizeTurns(array $turns, string $agentName): array
    {
        $result = [];

        foreach ($turns as $turn) {
            $startSeconds = $turn['start'];

            $result[] = [
                'speaker' => $turn['speaker'],
                'speaker_label' => $turn['speaker'] === 'agent' ? $agentName : 'Client',
                'text' => $turn['text'],
                'timestamp_seconds' => $startSeconds,
                'timestamp_label' => $this->formatTimestamp($startSeconds),
            ];
        }

        return $result;
    }

    private function formatTimestamp(int $seconds): string
    {
        return sprintf('%02d:%02d:%02d', intdiv($seconds, 3600), intdiv($seconds % 3600, 60), $seconds % 60);
    }

    /**
     * @param  array<int, array{text: string}>  $turns
     */
    private function countWords(array $turns): int
    {
        return array_sum(array_map(static fn (array $t): int => str_word_count($t['text']), $turns));
    }
}
