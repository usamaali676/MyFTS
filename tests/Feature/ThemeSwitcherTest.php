<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeSwitcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_customizer_script_is_loaded_so_the_theme_switcher_works(): void
    {
        Role::firstOrCreate(['id' => 1], ['name' => 'Reserved Admin Placeholder']);
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        Permission::create([
            'role_id' => $role->id,
            'name' => 'calltranscription',
            'create' => 0,
            'view' => 1,
            'edit' => 0,
            'delete' => 0,
        ]);

        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);

        // Any page built on layouts.dashboard exercises the same shared
        // layouts.partials.head include the Light/Dark/System switcher in
        // the navbar depends on -- without this script tag actually
        // rendering, window.templateCustomizer never exists client-side, so
        // main.js's style-switcher click handlers and icon never attach.
        $response = $this->actingAs($user)->get(route('calltranscription.index'));

        $response->assertOk();
        $response->assertSee('assets/vendor/js/template-customizer.js', false);
    }
}
