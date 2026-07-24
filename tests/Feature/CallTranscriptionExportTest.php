<?php

namespace Tests\Feature;

use App\Models\CallTranscription;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CallTranscriptionExportTest extends TestCase
{
    use RefreshDatabase;

    private function makeAuthorizedUser(): User
    {
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        Permission::create([
            'role_id' => $role->id,
            'name' => 'calltranscription',
            'create' => 1,
            'view' => 1,
            'edit' => 0,
            'delete' => 1,
        ]);

        return User::create([
            'name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    private function makeRecord(User $user): CallTranscription
    {
        return CallTranscription::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'agent_user_id' => $user->id,
            'agent_name_snapshot' => $user->name,
            'original_filename' => 'call.wav',
            'file_size_bytes' => 2048,
            'mime_type' => 'audio/wav',
            'status' => 'completed',
            'transcript_json' => [
                ['speaker' => 'agent', 'speaker_label' => $user->name, 'text' => 'Hello there.', 'timestamp_seconds' => 0, 'timestamp_label' => '00:00:00'],
                ['speaker' => 'client', 'speaker_label' => 'Client', 'text' => 'Hi, I need help.', 'timestamp_seconds' => 2, 'timestamp_label' => '00:00:02'],
            ],
            'word_count' => 7,
            'exchange_count' => 2,
            'created_by' => $user->id,
        ]);
    }

    public function test_txt_export_downloads_with_expected_content(): void
    {
        $user = $this->makeAuthorizedUser();
        $record = $this->makeRecord($user);

        $response = $this->actingAs($user)->get(route('calltranscription.export', ['uuid' => $record->uuid, 'format' => 'txt']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString('Hello there.', $response->getContent());
        $this->assertStringContainsString('Client:', $response->getContent());
    }

    public function test_pdf_export_downloads(): void
    {
        $user = $this->makeAuthorizedUser();
        $record = $this->makeRecord($user);

        $response = $this->actingAs($user)->get(route('calltranscription.export', ['uuid' => $record->uuid, 'format' => 'pdf']));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_docx_export_downloads(): void
    {
        $user = $this->makeAuthorizedUser();
        $record = $this->makeRecord($user);

        $response = $this->actingAs($user)->get(route('calltranscription.export', ['uuid' => $record->uuid, 'format' => 'docx']));

        $response->assertOk();
        $this->assertStringContainsString('wordprocessingml', $response->headers->get('Content-Type'));
    }

    public function test_unknown_format_returns_404(): void
    {
        $user = $this->makeAuthorizedUser();
        $record = $this->makeRecord($user);

        $response = $this->actingAs($user)->get(route('calltranscription.export', ['uuid' => $record->uuid, 'format' => 'exe']));

        $response->assertNotFound();
    }
}
