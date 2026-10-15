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
        // role_id 1 is the fixed admin role (PermissionMiddelware bypasses
        // all checks for it) and is permanently reserved in production
        // (seeded first, never deletable) -- reserve it here too so this
        // test's own role never accidentally lands on id 1 in the empty
        // test database and gets an unintended free pass.
        Role::firstOrCreate(['id' => 1], ['name' => 'Reserved Admin Placeholder']);
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

    public function test_store_transcribes_synchronously_and_returns_the_result(): void
    {
        // No REV_AI_TOKEN in .env.testing -- the service falls back to the
        // OpenAI pipeline, with no network call, deterministically.
        OpenAI::fake([
            TranscriptionResponse::fake([
                'text' => 'Hello.',
                'duration' => 2.0,
                'segments' => [
                    ['id' => 0, 'start' => 0.0, 'end' => 2.0, 'text' => 'Hello.'],
                ],
            ]),
            CreateResponse::fake([
                'choices' => [[
                    'message' => ['content' => json_encode(['labels' => ['agent']])],
                ]],
            ]),
        ]);

        $user = $this->makeUserWithPermission(canView: true, canCreate: true);
        $agent = $this->makeUserWithPermission(canView: true, canCreate: true);

        $response = $this->actingAs($user)->post(route('calltranscription.store'), [
            'audio' => $this->makeSilentWavUploadedFile(2),
            'agent_user_id' => $agent->id,
            'request_uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.agent_name', $agent->name);
        $response->assertJsonPath('data.status', 'completed');
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
