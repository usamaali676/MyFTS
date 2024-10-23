<?php

namespace Database\Seeders;

use App\Models\CompanyServices;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = ['Web Development', 'SEO', 'GMB', 'Landing Pages'];
        foreach ($services as $service) {
            CompanyServices::create(['name' => $service]);
        }
    }
}
