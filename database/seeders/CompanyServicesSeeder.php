<?php

namespace Database\Seeders;

use App\Models\CompanyServices;
use App\Models\MerchantAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = ['Hosting'];
        // $services = ['Activation Date'];
        foreach ($services as $service) {
            CompanyServices::create([
                'name' => $service,
                'price' => '100',
                'category'=> "Development",
        ]);
        }
    }
}
