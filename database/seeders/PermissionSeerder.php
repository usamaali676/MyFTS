<?php

namespace Database\Seeders;

use App\Helpers\GlobalHelper;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeerder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::all();
        $routeCollection = GlobalHelper::Permissions();
        foreach($routeCollection as $route){
            foreach($role as $item){
            if($item->id == 1){
                Permission::firstOrCreate([
                    'name' => $route,
                    'role_id' => $item->id,
                    'create' => 1,
                    'view' => 1,
                    'edit' => 1,
                    'delete' => 1,
                ]);

            }
            else{
                Permission::firstOrCreate([
                    'name' => $route,
                    'role_id' => $item->id,
                    'create' => 0,
                    'view' => 0,
                    'edit' => 0,
                    'delete' => 0,
                ]);
            }
            }
        }
    }
}
