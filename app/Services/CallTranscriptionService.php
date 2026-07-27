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
    // private function diarize(string $text, string $agentName): array
    // {
    //     $prompt = <<<PROMPT
    //         You are given a raw phone-call transcript with no speaker labels, between two people:
    //         - "{$agentName}" (the agent / call center representative)
    //         - the caller (their customer)

    //         Split the transcript into an ordered list of speaking turns. For each turn, decide whether
    //         it was spoken by the agent or the client based on conversational role (who is greeting and
    //         assisting versus who is requesting help or answering questions about their own account).

    //         Return strict JSON only, in this exact shape, no prose:
    //         {"turns": [{"speaker": "agent", "text": "..."}, {"speaker": "client", "text": "..."}]}

    //         Transcript:
    //         {$text}
    //         PROMPT;

    //     $attempt = 0;

    //     while (true) {
    //         try {
    //             $response = OpenAI::chat()->create([
    //                 'model' => self::DIARIZE_MODEL,
    //                 'temperature' => 0,
    //                 'response_format' => ['type' => 'json_object'],
    //                 'messages' => [
    //                     ['role' => 'system', 'content' => 'You split call transcripts into labeled speaker turns and respond with strict JSON only.'],
    //                     ['role' => 'user', 'content' => $prompt],
    //                 ],
    //             ]);

    //             $decoded = json_decode($response->choices[0]->message->content, true);
    //             $turns = $decoded['turns'] ?? null;

    //             if (!is_array($turns) || count($turns) === 0) {
    //                 throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
    //             }

    //             $mapped = array_values(array_filter(array_map(static function (array $t): array {
    //                 return [
    //                     'speaker' => ($t['speaker'] ?? '') === 'agent' ? 'agent' : 'client',
    //                     'text' => trim((string) ($t['text'] ?? '')),
    //                 ];
    //             }, $turns), static fn (array $t): bool => $t['text'] !== ''));

    //             if (count($mapped) === 0) {
    //                 throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
    //             }

    //             return $mapped;
    //         } catch (TranscriptionFailedException $e) {
    //             throw $e;
    //         } catch (\OpenAI\Exceptions\RateLimitException|\OpenAI\Exceptions\ServerException|\OpenAI\Exceptions\TransporterException $e) {
    //             if (++$attempt > self::MAX_RETRIES) {
    //                 Log::error('OpenAI call failed after max retries', [
    //                     'exception' => get_class($e),
    //                     'message' => $e->getMessage(),
    //                     'attempts' => $attempt,
    //                 ]);
    //                 throw new TranscriptionFailedException('The transcription service is temporarily unavailable. Please try again shortly.', 503);
    //             }
    //             usleep(300000 * $attempt);
    //         }
    //         catch (\OpenAI\Exceptions\ErrorException $e) {
    //             throw new TranscriptionFailedException('The transcription service encountered an error while analyzing speakers.', 422);
    //         }
    //     }
    // }

    private const DIARIZE_TIMEOUT_SECONDS = 60;

/**
 * @return array<int, array{speaker: string, text: string}>
 */
private function diarize(string $text, string $agentName): array
{
    // Split into lines the model can reference by index, instead of asking
    // it to retype the whole transcript (which is what was causing the
    // ~30s+ generation time and timeouts on longer calls).
    $lines = preg_split('/(?<=[.?!])\s+|\n+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
    $lines = array_values($lines);

    if (count($lines) === 0) {
        throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
    }

    $numbered = implode("\n", array_map(
        static fn (int $i, string $line): string => ($i + 1) . ': ' . $line,
        array_keys($lines),
        $lines,
    ));

    $prompt = <<<PROMPT
        You are given a phone-call transcript split into numbered lines, between two people:
        - "{$agentName}" (the agent / call center representative)
        - the caller (their customer)

        For each line number, decide whether it was spoken by the agent or the client, based on
        conversational role (who is greeting and assisting versus who is requesting help or
        answering questions about their own account). Group consecutive lines from the same
        speaker into a single turn.

        Return strict JSON only, no prose, in this exact shape:
        {"turns": [{"speaker": "agent", "lines": [1,2]}, {"speaker": "client", "lines": [3]}]}

        Numbered transcript:
        {$numbered}
        PROMPT;

    $attempt = 0;

    while (true) {
        try {
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
            $rawTurns = $decoded['turns'] ?? null;

            if (!is_array($rawTurns) || count($rawTurns) === 0) {
                throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
            }

            $mapped = [];
            foreach ($rawTurns as $t) {
                $speaker = ($t['speaker'] ?? '') === 'agent' ? 'agent' : 'client';
                $lineNumbers = is_array($t['lines'] ?? null) ? $t['lines'] : [];

                $turnText = trim(implode(' ', array_filter(array_map(
                    static fn ($n) => $lines[((int) $n) - 1] ?? null,
                    $lineNumbers,
                ))));

                if ($turnText !== '') {
                    $mapped[] = ['speaker' => $speaker, 'text' => $turnText];
                }
            }

            if (count($mapped) === 0) {
                throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
            }

            return $mapped;
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
