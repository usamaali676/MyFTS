<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'Executives')->first();

        for($i = 0; $i < 5; $i++) {
            User::create([
                'name' => 'ext '.($i+1),
                'email' => 'ext'.($i+1).'@firmtechsol.com',
                'password' => bcrypt('11111111'),
                'role_id' => $role->id,
            ]);
        }
    }
}
