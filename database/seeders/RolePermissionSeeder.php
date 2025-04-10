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
        // Create roles
        $adminRole = Role::create(['name' => 'Admin', 'guard_name' => 'api']);
        $userRole = Role::create(['name' => 'User', 'guard_name' => 'api']);
        $privateUserRole = Role::create(['name' => 'PrivateUser', 'guard_name' => 'api']);

        // Create permissions for trips
        $createTripPermission = Permission::create(['name' => 'trip.create', 'guard_name' => 'api']);
        $viewTripPermission = Permission::create(['name' => 'trip.list', 'guard_name' => 'api']);
        $updateTripPermission = Permission::create(['name' => 'trip.update', 'guard_name' => 'api']);
        $deleteTripPermission = Permission::create(['name' => 'trip.delete', 'guard_name' => 'api']);
        $endTripPermission = Permission::create(['name' => 'trip.ended', 'guard_name' => 'api']);

        // Create permissions for bookings
        $showBookingPermission = Permission::create(['name' => 'booking.show', 'guard_name' => 'api']);
        $acceptBookingPermission = Permission::create(['name' => 'booking.accept', 'guard_name' => 'api']);
        $rejectBookingPermission = Permission::create(['name' => 'booking.reject', 'guard_name' => 'api']);
        $cancelBookingPermission = Permission::create(['name' => 'booking.cancel', 'guard_name' => 'api']);


        // Assign permissions to the PrivateUser role
        $privateUserRole->givePermissionTo([
            $createTripPermission,
            $viewTripPermission,
            $updateTripPermission,
            $deleteTripPermission,
            $endTripPermission,
            $showBookingPermission,
            $acceptBookingPermission,
            $rejectBookingPermission,
            $cancelBookingPermission,
        ]);

        // Create an admin user
        $adminUser = User::create([
            'email' => 'admin@example.com',
            'password' => bcrypt('P@ssw0rd123'),
        ]);
        $adminUser->assignRole($adminRole);


        // Create a private user
        $privateUser = User::create([
            'email' => 'private@example.com',
            'password' => bcrypt('P@ssw0rd123'),
        ]);
        $privateUser->assignRole($privateUserRole);


        $privateUser->roles()->updateExistingPivot($privateUserRole->id, [
            'about_User' => 'This user is a private user',
            'car_Type' => 'SUV',
            'image_name' => 'ceb457975b293aa4bd9fd5d7a0dff8a2',
            'image_path' => '//i.pinimg.com/736x/ce/b4/57',
            'mime_type' => 'jpg',

        ]);
    }
}
