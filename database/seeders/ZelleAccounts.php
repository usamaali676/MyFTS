<?php

namespace Database\Seeders;

use App\Models\ZelleAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZelleAccounts extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $zelle = ['accounts@firmtech.biz'];
       foreach ($zelle as $acc){
        ZelleAccount::create([
            
            'name' => $acc
            
        ]);
       }
    }
}
