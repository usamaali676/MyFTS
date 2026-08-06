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
    private const DIARIZE_MODEL = 'gpt-4o';
    private const MAX_RETRIES = 2;
    private const LABEL_CHUNK_SIZE = 25;
    private const CONTEXT_LINES = 5;

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
     * @return array{0: array<int, array{text: string, start: int, pause_before: float}>, 1: ?int}
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
                $previousEnd = null;

                foreach ($response->segments as $segment) {
                    $text = trim($segment->text);

                    if ($text !== '') {
                        $pauseBefore = $previousEnd !== null ? max(0.0, $segment->start - $previousEnd) : 0.0;

                        $segments[] = [
                            'text' => $text,
                            'start' => (int) round($segment->start),
                            'pause_before' => round($pauseBefore, 1),
                        ];
                    }

                    $previousEnd = $segment->end;
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
        $pauses = array_column($segments, 'pause_before');

        $labels = $this->labelLines($lines, $pauses, $agentName);
        $turns = $this->groupLabeledLines($lines, $starts, $labels);

        return $this->reconcileTurnsWithFullContext($turns, $agentName);
    }

    /**
     * @param  array<int, string>  $lines
     * @param  array<int, float>  $pauses
     * @return array<int, string> one 'agent'|'client' label per line, same order/length as $lines
     */
    private function labelLines(array $lines, array $pauses, string $agentName): array
    {
        $labels = [];
        $context = [];

        foreach (array_chunk($lines, self::LABEL_CHUNK_SIZE) as $chunkIndex => $chunk) {
            $pauseChunk = array_slice($pauses, $chunkIndex * self::LABEL_CHUNK_SIZE, count($chunk));
            $chunkLabels = $this->labelChunk($chunk, $pauseChunk, $agentName, $context);
            $labels = array_merge($labels, $chunkLabels);

            foreach ($chunk as $i => $line) {
                $context[] = ['speaker' => $chunkLabels[$i] ?? 'client', 'text' => $line];
            }
            $context = array_slice($context, -self::CONTEXT_LINES);
        }

        return $labels;
    }

    /**
     * @param  array<int, string>  $lines
     * @param  array<int, float>  $pauses
     * @return array<int, string> one 'agent'|'client' label per line, same order/length as $lines
     */
    /**
     * @param  array<int, array{speaker: string, text: string}>  $context  already-labeled lines immediately before this chunk, oldest first, at most self::CONTEXT_LINES entries
     */
    private function labelChunk(array $lines, array $pauses, string $agentName, array $context): array
    {
        $count = count($lines);
        $numbered = implode("\n", array_map(
            static fn (int $i, string $line, float $pause): string => ($i + 1)
                . ' [pause before this line: ' . number_format($pause, 1) . 's]: ' . $line,
            array_keys($lines),
            $lines,
            $pauses,
        ));

        $contextBlock = '';
        if ($context !== []) {
            $renderedContext = implode("\n", array_map(
                static fn (array $c): string => '"' . $c['speaker'] . '": "' . $c['text'] . '"',
                $context,
            ));

            $contextBlock = "For continuity only (these lines are from just before this excerpt and are"
                . " already labeled -- do NOT include them in your output), oldest first:\n{$renderedContext}\n"
                . "Keep applying the same speaker identity consistently; do not swap which name means agent"
                . " vs client partway through.\n\n";
        }

        $basePrompt = <<<PROMPT
            You are given a phone-call transcript mechanically split into {$count} numbered text fragments
            by PAUSES IN THE RAW AUDIO -- not by who is speaking. Many consecutive fragments are one
            uninterrupted sentence or monologue from the SAME person, cut apart only because of a breath,
            filler word, or the transcription engine's own chunking. A fragment that ends mid-word,
            mid-thought, or without a natural sentence ending is almost always continued by the very next
            fragment from the SAME speaker.

            Each fragment is annotated with the real silence gap (in seconds) measured between the end of
            the previous fragment and the start of this one. A near-zero pause means the same speaker very
            likely kept talking without a real break; a longer pause more often (but not always) lines up
            with a change of speaker. Treat this as a supporting signal alongside the words themselves, not
            as a strict rule on its own.

            Two people are talking:
            - "{$agentName}" (the agent / call center representative)
            - the caller (their customer)

            For each fragment, decide who is speaking. Default to keeping the SAME speaker as the previous
            fragment. Only switch speakers when the content clearly signals a genuine handoff -- e.g. a
            direct reply or acknowledgment to what was just said, a shift from the agent's pitch to the
            client asking or answering something about their own account, or a statement that only makes
            sense as a response to the fragment before it. Do not alternate speakers just because a
            fragment boundary exists.

            {$contextBlock}Return strict JSON only, no prose, in this exact shape — exactly {$count} labels, one per
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
     * Best-effort final pass: re-reads the WHOLE assembled transcript (turns,
     * not raw segments) with full conversational context and corrects any
     * turn whose speaker looks wrong now that the complete conversation is
     * visible -- catches drift the forward-only chunked pass above can't see.
     * Never throws -- on any failure or malformed response this returns
     * $turns unchanged, since a chunk-labeled transcript is still a valid,
     * already-paid-for result.
     *
     * @param  array<int, array{speaker: string, text: string, start: int}>  $turns
     * @return array<int, array{speaker: string, text: string, start: int}>
     */
    private function reconcileTurnsWithFullContext(array $turns, string $agentName): array
    {
        $count = count($turns);

        if ($count < 2) {
            return $turns;
        }

        $numbered = implode("\n", array_map(
            static fn (int $i, array $turn): string => "[{$i}] " . ($turn['speaker'] === 'agent' ? $agentName : 'the caller') . ': ' . $turn['text'],
            array_keys($turns),
            $turns,
        ));

        $prompt = <<<PROMPT
            You are given a complete phone-call transcript, already split into {$count} numbered turns and
            labeled by speaker. Two people are talking: "{$agentName}" (the agent) and the caller (their
            customer). Re-read the ENTIRE conversation for full context and identify any turn whose speaker
            label looks wrong now that you can see the whole call -- for example a turn that reads like a
            direct reply to a question the agent just asked, but is currently labeled as the agent's own
            turn.

            Return strict JSON only, no prose, in this exact shape -- exactly {$count} labels, one per
            line, in the same order as the transcript, reflecting your corrected judgment (repeat the
            existing label for any turn you agree with):
            {"labels": ["agent", "client", "client", "agent"]}

            Numbered transcript:
            {$numbered}
            PROMPT;

        try {
            $response = OpenAI::chat()->create([
                'model' => self::DIARIZE_MODEL,
                'temperature' => 0,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You review phone-call transcripts for speaker-labeling consistency and respond with strict JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $decoded = json_decode($response->choices[0]->message->content, true);
            $labels = $decoded['labels'] ?? null;

            if (!is_array($labels) || count($labels) !== $count) {
                return $turns;
            }

            $corrected = [];
            foreach (array_values($turns) as $i => $turn) {
                $turn['speaker'] = ($labels[$i] ?? $turn['speaker']) === 'agent' ? 'agent' : 'client';
                $corrected[] = $turn;
            }

            return $this->mergeAdjacentSameSpeakerTurns($corrected);
        } catch (\Throwable $e) {
            Log::warning('Transcript reconciliation pass failed; using chunk-labeled turns as-is', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return $turns;
        }
    }

    /**
     * @param  array<int, array{speaker: string, text: string, start: int}>  $turns
     * @return array<int, array{speaker: string, text: string, start: int}>
     */
    private function mergeAdjacentSameSpeakerTurns(array $turns): array
    {
        $merged = [];

        foreach ($turns as $turn) {
            $last = count($merged) - 1;

            if ($last >= 0 && $merged[$last]['speaker'] === $turn['speaker']) {
                $merged[$last]['text'] = trim($merged[$last]['text'] . ' ' . $turn['text']);
                continue;
            }

            $merged[] = $turn;
        }

        return $merged;
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
