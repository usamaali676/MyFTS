<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Audio\TranscriptionResponse;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\Concerns\MakesSilentWav;
use Tests\TestCase;

class CallTranscriptionControllerTest extends TestCase
{
    use RefreshDatabase;
    use MakesSilentWav;

    private function makeUserWithPermission(bool $canView, bool $canCreate): User
    {
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        Permission::create([
            'role_id' => $role->id,
            'name' => 'calltranscription',
            'create' => $canCreate ? 1 : 0,
            'view' => $canView ? 1 : 0,
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

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('calltranscription.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_denied_without_view_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: false, canCreate: false);

        $response = $this->actingAs($user)->get(route('calltranscription.index'));

        $response->assertRedirect(route('home'));
    }

    public function test_index_allowed_with_view_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);

        $response = $this->actingAs($user)->get(route('calltranscription.index'));

        $response->assertOk();
    }

    public function test_store_generates_transcript_for_authorized_user(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);
        $agent = $this->makeUserWithPermission(canView: true, canCreate: true);

        OpenAI::fake([
            TranscriptionResponse::fake(['text' => 'Hello there.']),
            CreateResponse::fake([
                'choices' => [[
                    'message' => ['content' => json_encode(['turns' => [['speaker' => 'agent', 'text' => 'Hello there.']]])],
                ]],
            ]),
        ]);

        $response = $this->actingAs($user)->post(route('calltranscription.store'), [
            'audio' => $this->makeSilentWavUploadedFile(2),
            'agent_user_id' => $agent->id,
            'request_uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.agent_name', $agent->name);
        $response->assertJsonPath('data.turns.0.speaker_label', $agent->name);
    }

    public function test_store_denied_without_create_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: false);
        $agent = $this->makeUserWithPermission(canView: true, canCreate: true);

        $response = $this->actingAs($user)->post(route('calltranscription.store'), [
            'audio' => $this->makeSilentWavUploadedFile(1),
            'agent_user_id' => $agent->id,
            'request_uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response->assertRedirect(route('home'));
    }

    public function test_store_rejects_unsupported_file_type(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);
        $agent = $this->makeUserWithPermission(canView: true, canCreate: true);

        $response = $this->actingAs($user)->post(route('calltranscription.store'), [
            'audio' => \Illuminate\Http\UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
            'agent_user_id' => $agent->id,
            'request_uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response->assertSessionHasErrors('audio');
    }
}
