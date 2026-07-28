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

    public function test_it_is_idempotent_on_request_uuid(): void
    {
        $agent = $this->makeAgent();

        OpenAI::fake([
            TranscriptionResponse::fake(['text' => 'Hello.']),
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
            TranscriptionResponse::fake(['text' => '']),
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
