<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CallTranscriptionSidebarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // HomeController::index() hard-codes a lookup for a role literally named
        // "TSR" (pre-existing behavior, unrelated to this feature) — seed it so
        // hitting the home route in this isolated test DB doesn't crash.
        Role::firstOrCreate(['name' => 'TSR']);
    }

    private function makeUser(?bool $canView): User
    {
        Role::firstOrCreate(['id' => 1], ['name' => 'Reserved Admin Placeholder']);
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        if ($canView !== null) {
            Permission::create([
                'role_id' => $role->id,
                'name' => 'calltranscription',
                'create' => 0,
                'view' => $canView ? 1 : 0,
                'edit' => 0,
                'delete' => 0,
            ]);
        }

        return User::create([
            'name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    public function test_sidebar_shows_link_when_permission_granted(): void
    {
        $user = $this->makeUser(canView: true);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('calltranscription.index'), false);
    }

    public function test_sidebar_hides_link_when_permission_missing(): void
    {
        $user = $this->makeUser(canView: null);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertDontSee(route('calltranscription.index'), false);
    }
}
