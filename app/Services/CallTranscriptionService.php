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
    private const TRANSCRIBE_MODEL = 'gpt-4o-mini-transcribe';
    private const DIARIZE_MODEL = 'gpt-4o-mini';
    private const MAX_RETRIES = 2;

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
            $durationSeconds = $this->readDuration($storedPath);
            $text = $this->transcribe($storedPath);
            $rawTurns = $this->diarize($text, $agent->name);
            $turns = $this->finalizeTurns($rawTurns, $agent->name, $durationSeconds);

            $record->update([
                'status' => 'completed',
                'transcript_json' => $turns,
                'duration_seconds' => $durationSeconds,
                'word_count' => $this->countWords($turns),
                'exchange_count' => count($turns),
                'processing_time_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
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

    private function transcribe(string $path): string
    {
        $attempt = 0;

        while (true) {
            try {
                $response = OpenAI::audio()->transcribe([
                    'model' => self::TRANSCRIBE_MODEL,
                    'file' => fopen($path, 'r'),
                    'response_format' => 'json',
                ]);

                $text = trim($response->text);

                if ($text === '') {
                    throw new TranscriptionFailedException('No speech was detected in the uploaded recording.', 422);
                }

                return $text;
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
     * @return array<int, array{speaker: string, text: string}>
     */
    private function diarize(string $text, string $agentName): array
    {
        // Split into sentence-level lines and ask the model to label EVERY line
        // individually (not choose its own grouping). Letting the model pick
        // turn boundaries made it lazily merge most of a long call into 3-4
        // giant blocks instead of real per-exchange turns. Forcing one label
        // per line, then grouping consecutive same-speaker lines ourselves in
        // PHP, keeps turns granular and timestamps meaningfully tied to the
        // actual flow of the conversation.
        $lines = preg_split('/(?<=[.?!])\s+|\n+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $lines = array_values($lines);

        if (count($lines) === 0) {
            throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
        }

        $labels = $this->labelLines($lines, $agentName);

        return $this->groupLabeledLines($lines, $labels);
    }

    /**
     * @param  array<int, string>  $lines
     * @return array<int, string> one 'agent'|'client' label per line, same order/length as $lines
     */
    private function labelLines(array $lines, string $agentName): array
    {
        $count = count($lines);
        $numbered = implode("\n", array_map(
            static fn (int $i, string $line): string => ($i + 1) . ': ' . $line,
            array_keys($lines),
            $lines,
        ));

        $basePrompt = <<<PROMPT
            You are given a phone-call transcript split into {$count} numbered lines, between two people:
            - "{$agentName}" (the agent / call center representative)
            - the caller (their customer)

            Real phone calls alternate speakers frequently, often every one or two sentences. Label EVERY
            line individually based on conversational role (who is greeting/assisting versus who is
            requesting help or answering questions about their own account). Do not lump many lines
            together under one speaker unless they are clearly one uninterrupted monologue with no
            response from the other person.

            Return strict JSON only, no prose, in this exact shape — exactly {$count} labels, one per
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
     * @param  array<int, string>  $labels
     * @return array<int, array{speaker: string, text: string}>
     */
    private function groupLabeledLines(array $lines, array $labels): array
    {
        $turns = [];
        $currentSpeaker = null;
        $buffer = [];

        foreach ($lines as $i => $line) {
            $speaker = $labels[$i] ?? ($currentSpeaker ?? 'client');

            if ($currentSpeaker !== null && $speaker !== $currentSpeaker) {
                $turns[] = ['speaker' => $currentSpeaker, 'text' => trim(implode(' ', $buffer))];
                $buffer = [];
            }

            $currentSpeaker = $speaker;
            $buffer[] = $line;
        }

        if ($buffer !== []) {
            $turns[] = ['speaker' => $currentSpeaker, 'text' => trim(implode(' ', $buffer))];
        }

        if (count($turns) === 0) {
            throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
        }

        return $turns;
    }

    /**
     * @param  array<int, array{speaker: string, text: string}>  $turns
     * @return array<int, array{speaker: string, speaker_label: string, text: string, timestamp_seconds: ?int, timestamp_label: ?string}>
     */
    private function finalizeTurns(array $turns, string $agentName, ?int $durationSeconds): array
    {
        $totalWords = max(1, array_sum(array_map(static fn (array $t): int => str_word_count($t['text']), $turns)));
        $elapsedWords = 0;
        $result = [];

        foreach ($turns as $turn) {
            $words = str_word_count($turn['text']);
            $startSeconds = $durationSeconds !== null
                ? (int) round(($elapsedWords / $totalWords) * $durationSeconds)
                : null;
            $elapsedWords += $words;

            $result[] = [
                'speaker' => $turn['speaker'],
                'speaker_label' => $turn['speaker'] === 'agent' ? $agentName : 'Client',
                'text' => $turn['text'],
                'timestamp_seconds' => $startSeconds,
                'timestamp_label' => $startSeconds !== null ? $this->formatTimestamp($startSeconds) : null,
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
