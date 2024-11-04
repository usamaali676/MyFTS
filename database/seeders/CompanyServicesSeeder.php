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
        $services = ['Firm Tech Sol', 'Firmtech LLC', 'Witrobo', 'Firm Tech Biz', 'Orion Logix'];
        foreach ($services as $service) {
            MerchantAccount::create(['name' => $service]);
        }
    }
}
