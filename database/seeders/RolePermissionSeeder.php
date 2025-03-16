<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {



        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'User']);
        $privetuserRole = Role::create(['name' => 'PrivateUser']);


        $editPermission = Permission::create(['name' => 'trip.create']);
        $viewPermission = Permission::create(['name' => 'trip.list']);
        $updatPermission = Permission::create(['name' => 'trip.update']);
        $deletPermission = Permission::create(['name' => 'trip.delete']);


        $privetuserRole->givePermissionTo($editPermission, $viewPermission, $updatPermission, $deletPermission);


    }
}
