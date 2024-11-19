<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankTransfer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zelle = ['Account1', 'Account2', 'Account3'];
        foreach ($zelle as $acc){
         BankAccount::create([         
             'name' => $acc
             
         ]);
        }
     }
    }

