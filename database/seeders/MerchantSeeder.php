<?php

namespace Database\Seeders;

use App\Models\MerchantAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchant = ['Firm Tech Sol', 'Firmtech LLC', 'Witrobo', 'Firm Tech Biz',  'Orion Logix'];
        foreach ($merchant as $item) {
            MerchantAccount::create([
                'name' => $item,
            ]);
        }
    }
}
