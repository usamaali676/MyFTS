<?php

namespace Tests\Feature;

use App\Exceptions\TranscriptionFailedException;
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

        $file = $this->makeSilentWavUploadedFile(4);

        $record = app(CallTranscriptionService::class)->generate($file, $agent, 'req-uuid-1', $agent->id);

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

    public function test_it_sends_pause_duration_and_uses_the_upgraded_model_for_labeling(): void
    {
        $agent = $this->makeAgent();

        OpenAI::fake([
            TranscriptionResponse::fake([
                'text' => 'Hello thank you for calling. Hi I need help with my order. I would be happy to assist.',
                'duration' => 4.0,
                'segments' => [
                    ['id' => 0, 'start' => 0.0, 'end' => 1.0, 'text' => 'Hello thank you for calling.'],
                    ['id' => 1, 'start' => 1.0, 'end' => 3.0, 'text' => 'Hi I need help with my order.'],
                    ['id' => 2, 'start' => 3.6, 'end' => 4.0, 'text' => 'I would be happy to assist.'],
                ],
            ]),
            CreateResponse::fake([
                'choices' => [[
                    'message' => ['content' => json_encode(['labels' => ['agent', 'client', 'agent']])],
                ]],
            ]),
        ]);

        $file = $this->makeSilentWavUploadedFile(4);

        app(CallTranscriptionService::class)->generate($file, $agent, 'req-uuid-pause', $agent->id);

        OpenAI::assertSent(\OpenAI\Resources\Chat::class, function (string $method, array $parameters): bool {
            if ($parameters['model'] !== 'gpt-4o') {
                return false;
            }

            $prompt = $parameters['messages'][1]['content'];

            // Segment 0 is the first line -> 0.0s pause. Segment 2 starts at
            // 3.6s, 0.6s after segment 1 ends at 3.0s -> 0.6s pause.
            return str_contains($prompt, '[pause before this line: 0.0s]')
                && str_contains($prompt, '[pause before this line: 0.6s]');
        });
    }

    public function test_it_carries_the_last_five_labeled_lines_across_a_chunk_boundary(): void
    {
        $agent = $this->makeAgent();

        $segments = [];
        for ($i = 0; $i < 30; $i++) {
            $segments[] = ['id' => $i, 'start' => (float) $i, 'end' => (float) ($i + 1), 'text' => 'Line ' . ($i + 1) . '.'];
        }

        $chunk1Labels = array_merge(array_fill(0, 20, 'agent'), array_fill(0, 5, 'client'));
        $chunk2Labels = array_fill(0, 5, 'client');

        OpenAI::fake([
            TranscriptionResponse::fake([
                'text' => implode(' ', array_column($segments, 'text')),
                'duration' => 30.0,
                'segments' => $segments,
            ]),
            CreateResponse::fake([
                'choices' => [['message' => ['content' => json_encode(['labels' => $chunk1Labels])]]],
            ]),
            CreateResponse::fake([
                'choices' => [['message' => ['content' => json_encode(['labels' => $chunk2Labels])]]],
            ]),
        ]);

        $file = $this->makeSilentWavUploadedFile(30);

        app(CallTranscriptionService::class)->generate($file, $agent, 'req-uuid-context', $agent->id);

        OpenAI::assertSent(\OpenAI\Resources\Chat::class, function (string $method, array $parameters): bool {
            $prompt = $parameters['messages'][1]['content'];

            // Lines 21-25 (index 20-24) were the last 5 labeled lines of
            // chunk 1, all labeled 'client' -- they must appear as context
            // in whichever call is chunk 2's.
            return str_contains($prompt, '"client": "Line 21."')
                && str_contains($prompt, '"client": "Line 25."')
                && !str_contains($prompt, '"client": "Line 20."');
        });
    }

    public function test_it_reconciles_speaker_labels_using_full_transcript_context(): void
    {
        $agent = $this->makeAgent();

        OpenAI::fake([
            TranscriptionResponse::fake([
                'text' => 'Hello thank you for calling. Hi I need help with my order. Sure I can help with that.',
                'duration' => 4.0,
                'segments' => [
                    ['id' => 0, 'start' => 0.0, 'end' => 1.0, 'text' => 'Hello thank you for calling.'],
                    ['id' => 1, 'start' => 1.0, 'end' => 3.0, 'text' => 'Hi I need help with my order.'],
                    ['id' => 2, 'start' => 3.0, 'end' => 4.0, 'text' => 'Sure I can help with that.'],
                ],
            ]),
            CreateResponse::fake([
                'choices' => [['message' => ['content' => json_encode(['labels' => ['agent', 'client', 'agent']])]]],
            ]),
            CreateResponse::fake([
                'choices' => [['message' => ['content' => json_encode(['labels' => ['agent', 'agent', 'agent']])]]],
            ]),
        ]);

        $file = $this->makeSilentWavUploadedFile(4);

        $record = app(CallTranscriptionService::class)->generate($file, $agent, 'req-uuid-reconcile', $agent->id);

        $turns = $record->transcript_json;
        $this->assertCount(1, $turns);
        $this->assertSame('John Smith', $turns[0]['speaker_label']);
        $this->assertSame(
            'Hello thank you for calling. Hi I need help with my order. Sure I can help with that.',
            $turns[0]['text']
        );
        $this->assertSame(0, $turns[0]['timestamp_seconds']);
    }

    public function test_it_keeps_chunk_labeled_turns_when_reconciliation_pass_fails(): void
    {
        $agent = $this->makeAgent();

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
                'choices' => [['message' => ['content' => json_encode(['labels' => ['agent', 'client', 'agent']])]]],
            ]),
            new \Exception('simulated reconciliation outage'),
        ]);

        $file = $this->makeSilentWavUploadedFile(4);

        $record = app(CallTranscriptionService::class)->generate($file, $agent, 'req-uuid-reconcile-fail', $agent->id);

        $this->assertSame('completed', $record->status);
        $turns = $record->transcript_json;
        $this->assertCount(3, $turns);
        $this->assertSame('John Smith', $turns[0]['speaker_label']);
        $this->assertSame('Client', $turns[1]['speaker_label']);
        $this->assertSame('John Smith', $turns[2]['speaker_label']);
    }

    public function test_it_is_idempotent_on_request_uuid(): void
    {
        $agent = $this->makeAgent();

        OpenAI::fake([
            TranscriptionResponse::fake([
                'text' => 'Hello.',
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
        $file = $this->makeSilentWavUploadedFile(1);

        $first = $service->generate($file, $agent, 'dup-uuid', $agent->id);
        $second = $service->generate($this->makeSilentWavUploadedFile(1), $agent, 'dup-uuid', $agent->id);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, CallTranscription::count());
    }

    public function test_it_fails_when_transcription_returns_no_speech(): void
    {
        $agent = $this->makeAgent();

        OpenAI::fake([
            TranscriptionResponse::fake(['text' => '', 'segments' => []]),
        ]);

        $file = $this->makeSilentWavUploadedFile(1);

        $this->expectException(TranscriptionFailedException::class);

        try {
            app(CallTranscriptionService::class)->generate($file, $agent, 'req-uuid-empty', $agent->id);
        } finally {
            $this->assertSame('failed', CallTranscription::where('uuid', 'req-uuid-empty')->first()->status);
        }
    }
}
