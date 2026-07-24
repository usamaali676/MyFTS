<?php

namespace Tests\Feature;

use App\Models\CallTranscription;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CallTranscriptionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_transcript_json_as_array(): void
    {
        $role = Role::create(['name' => 'Test Role']);
        $agent = User::create([
            'name' => 'John Smith',
            'email' => 'john@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);

        $record = CallTranscription::create([
            'uuid' => 'test-uuid-1',
            'agent_user_id' => $agent->id,
            'agent_name_snapshot' => $agent->name,
            'original_filename' => 'call.mp3',
            'file_size_bytes' => 1024,
            'mime_type' => 'audio/mpeg',
            'status' => 'completed',
            'transcript_json' => [
                ['speaker' => 'agent', 'speaker_label' => 'John Smith', 'text' => 'Hello.', 'timestamp_seconds' => 0, 'timestamp_label' => '00:00:00'],
            ],
            'word_count' => 1,
            'exchange_count' => 1,
            'created_by' => $agent->id,
        ]);

        $fresh = CallTranscription::find($record->id);

        $this->assertIsArray($fresh->transcript_json);
        $this->assertSame('John Smith', $fresh->transcript_json[0]['speaker_label']);
        $this->assertSame('completed', $fresh->status);
    }
}
