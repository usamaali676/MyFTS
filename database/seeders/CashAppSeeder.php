<?php

namespace Database\Seeders;

use App\Models\Cashapp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zelle = ['App1', 'App2', 'App3'];
        foreach ($zelle as $acc){
         Cashapp::create([
             'name' => $acc
         ]);
        }
     }
    }
