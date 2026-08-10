<?php

namespace Tests\Feature;

use App\Models\CallTranscription;
use App\Models\Role;
use App\Models\User;
use App\Services\CallTranscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Audio\TranscriptionResponse;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\Concerns\MakesSilentWav;
use Tests\TestCase;

class CallTranscriptionServiceTest extends TestCase
{
    use RefreshDatabase;
    use MakesSilentWav;

    private function makeAgent(): User
    {
        Role::firstOrCreate(['id' => 1], ['name' => 'Reserved Admin Placeholder']);
        $role = Role::create(['name' => 'Test Role']);

        return User::create([
            'name' => 'John Smith',
            'email' => 'john+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    public function test_it_transcribes_diarizes_and_estimates_timestamps(): void
    {
        $agent = $this->makeAgent();

        // No REV_AI_TOKEN in .env.testing -- transcribeViaRevAi() returns null
        // immediately (not configured), with no network call, so these tests
        // deterministically exercise the OpenAI-fallback path.
        OpenAI::fake([
            TranscriptionResponse::fake([
                'text' => 'Hello thank you for calling. Hi I need help with my order. I would be happy to assist.',
                'duration' => 4.0,
                'segments' => [
                    ['id' => 0, 'start' => 0.0, 'end' => 1.0, 'text' => 'Hello thank you for calling.'],
                    ['id' => 1, 'start' => 1.0, 'end' => 3.0, 'text' => 'Hi I need help with my order.'],
                    ['id' => 2, 'start' => 3.0, 'end' => 4.0, 'text' => 'I would be happy to assist.'],
                ],
            ]),
            CreateResponse::fake([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'labels' => ['agent', 'client', 'agent'],
                        ]),
                    ],
                ]],
            ]),
        ]);

        $service = app(CallTranscriptionService::class);
        $record = $service->generate($this->makeSilentWavUploadedFile(4), $agent, 'req-uuid-1', $agent->id);

        $this->assertSame('completed', $record->status);
        $this->assertSame(4, $record->duration_seconds);
        $this->assertSame(3, $record->exchange_count);
        $this->assertSame(18, $record->word_count);

        $turns = $record->transcript_json;
        $this->assertSame('John Smith', $turns[0]['speaker_label']);
        $this->assertSame('Client', $turns[1]['speaker_label']);
        $this->assertSame('John Smith', $turns[2]['speaker_label']);

        $this->assertSame(0, $turns[0]['timestamp_seconds']);
        $this->assertSame(1, $turns[1]['timestamp_seconds']);
        $this->assertSame(3, $turns[2]['timestamp_seconds']);
        $this->assertSame('00:00:03', $turns[2]['timestamp_label']);
    }

    public function test_it_is_idempotent_on_request_uuid(): void
    {
        $agent = $this->makeAgent();

        // No REV_AI_TOKEN in .env.testing -- transcribeViaRevAi() returns null
        // immediately (not configured), with no network call, so this test
        // deterministically exercises the OpenAI-fallback path.
        OpenAI::fake([
            TranscriptionResponse::fake([
                'text' => 'Hello.',
                'duration' => 1.0,
                'segments' => [
                    ['id' => 0, 'start' => 0.0, 'end' => 1.0, 'text' => 'Hello.'],
                ],
            ]),
            CreateResponse::fake([
                'choices' => [[
                    'message' => ['content' => json_encode(['labels' => ['agent']])],
                ]],
            ]),
        ]);

        $service = app(CallTranscriptionService::class);

        // The first call fully completes synchronously, so the second call
        // with the same uuid short-circuits on the now-'completed' record
        // instead of processing the (unfaked) second upload a second time.
        $first = $service->generate($this->makeSilentWavUploadedFile(1), $agent, 'dup-uuid', $agent->id);
        $second = $service->generate($this->makeSilentWavUploadedFile(1), $agent, 'dup-uuid', $agent->id);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, CallTranscription::count());
    }

    public function test_it_marks_the_record_failed_when_transcription_returns_no_speech(): void
    {
        $agent = $this->makeAgent();

        // No REV_AI_TOKEN in .env.testing -- transcribeViaRevAi() returns null
        // immediately (not configured), with no network call, so these tests
        // deterministically exercise the OpenAI-fallback path.
        OpenAI::fake([
            // OverrideStrategy::Replace, not the default Merge (array_replace_recursive):
            // merging an empty array into the fixture's default one-segment array is a
            // no-op (nothing to recursively replace), so it silently keeps that default
            // non-empty segment instead of actually producing zero segments.
            TranscriptionResponse::fake(
                ['text' => '', 'segments' => []],
                strategy: \OpenAI\Testing\Enums\OverrideStrategy::Replace,
            ),
        ]);

        $service = app(CallTranscriptionService::class);

        // generate() never throws -- any unrecoverable failure just marks
        // the record failed instead.
        $record = $service->generate($this->makeSilentWavUploadedFile(1), $agent, 'req-uuid-empty', $agent->id);

        $this->assertSame('failed', $record->status);
        $this->assertSame('No speech was detected in the uploaded recording.', $record->error_message);
    }
}
