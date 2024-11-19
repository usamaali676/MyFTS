<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $role = Role::where('name', 'Executives')->first();

        $user = [
            [
                'name' => 'Administrator',
                'email' => 'admin@admin.com',
                'password' => bcrypt('11111111'),
                'role_id' => 1,
            ],
            [
                'name' => "Micheal Carter - Muhammad Faraz",
                'email' => "tsr8@firmtechcol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 2,
            ],
            [
                'name' => "Justin - Muhammad Muneeb",
                'email' => "tsr5@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 2,
            ],
            [
                'name' => "William - Taha Afzal",
                'email' => "tsr17@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 2,
            ],
            [
                'name' => "David - Tahir Fareed",
                'email' => "tsr18@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 2,
            ],
            [
                'name' => "Jessica - Kinza",
                'email' => "tsr14@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 2,
            ],
            [
                'name' => "Michelle - Saba Saleem",
                'email' => "tsr10@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 2,
            ],
            [
                'name' => "Jimmy - Abdul Rehman",
                'email' => "csr1@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 4,
            ],
            [
                'name' => "Daniel - Sheikh Mohsin",
                'email' => "daniel@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 3,
            ],
            [
                'name' => "Sam - Obaid Hassan",
                'email' => "tsr13@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 4,
            ],
            [
                'name' => "Michael Davis - Muhammad Usman",
                'email' => "tsr2@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 4,
            ],
            [
                'name' => "John - Azhar abbas",
                'email' => "tsr3@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 5,
            ],
            [
                'name' => "Henry - Tanzeel ur Rehman",
                'email' => "tsr6@firmtechsol.com",
                'password' => bcrypt('11111111'),
                'role_id' => 5,
            ],
            [
                'name' => "Jeff - Rameez Hassan",
                'email' => "jeff@firmtechllc.com",
                'password' => bcrypt('11111111'),
                'role_id' => 5,
            ],
        ];

        foreach ($user as $key => $value) {
            User::create([
                'name' => $value['name'],
                'email' => $value['email'],
                'password' => $value['password'],
                'role_id' => $value['role_id'],
            ]);
        }


    }
}
