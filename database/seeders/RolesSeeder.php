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
        $route = GlobalHelper::Permissions();
        dd($route);
    }
}
