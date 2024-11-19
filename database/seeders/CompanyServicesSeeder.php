<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Cashapp;
use App\Models\CompanyServices;
use App\Models\MerchantAccount;
use App\Models\ZelleAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = ['Account1', 'Account2', 'Account3'];
        foreach ($services as $service) {
            BankAccount::create([
                'name' => $service,
                
        ]);
        }
    }
}
