<?php

namespace Database\Seeders;

use App\Helpers\GlobalHelper;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Creator','TSR', 'Executives', 'Customer Support', 'Closer'];
        foreach ($roles as $role) {
            Role::create([
                'name' => $role,

        ]);
        }
    }
}
