<?php

namespace App\Services;

use App\Exceptions\TranscriptionFailedException;
use App\Models\CallTranscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
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

    public function __construct(
        private readonly TranscriptComplianceService $complianceService,
    ) {}

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

        $startedAt = microtime(true);

        // Move the upload to a stable local path once, up front. The PHP temp-upload
        // file behind $file->getRealPath() is not reliable to read from twice in the
        // same request (e.g. it can transiently fail while an AV scan holds a lock
        // on it on Windows), and it also has no file extension, which the Whisper
        // API needs to detect the audio format. Working from one durable copy fixes
        // both problems.
        $extension = $file->getClientOriginalExtension() ?: 'mp3';
        $storedDir = storage_path('app/tmp/call-transcriptions');
        if (!is_dir($storedDir)) {
            mkdir($storedDir, 0755, true);
        }
        $storedName = $requestUuid . '.' . $extension;
        $file->move($storedDir, $storedName);
        $storedPath = $storedDir . DIRECTORY_SEPARATOR . $storedName;

        try {
            [$segments, $durationSeconds] = $this->transcribe($storedPath);
            $durationSeconds ??= $this->readDuration($storedPath);
            $rawTurns = $this->diarize($segments, $agent->name);
            $turns = $this->finalizeTurns($rawTurns, $agent->name);

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
            throw $e;
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
            throw new TranscriptionFailedException('An unexpected error occurred while processing the recording.', 500);
        } finally {
            if (is_file($storedPath)) {
                @unlink($storedPath);
            }
        }

        return $record->fresh();
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
