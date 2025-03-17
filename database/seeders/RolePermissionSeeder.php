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
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'User']);
        $privateUserRole = Role::create(['name' => 'PrivateUser']);

        // Create permissions for trips
        $createTripPermission = Permission::create(['name' => 'trip.create']);
        $viewTripPermission = Permission::create(['name' => 'trip.list']);
        $updateTripPermission = Permission::create(['name' => 'trip.update']);
        $deleteTripPermission = Permission::create(['name' => 'trip.delete']);
        $endTripPermission = Permission::create(['name' => 'trip.ended']);

        // Create permissions for bookings
        $showBookingPermission = Permission::create(['name' => 'booking.show']);
        $acceptBookingPermission = Permission::create(['name' => 'booking.accept']);
        $rejectBookingPermission = Permission::create(['name' => 'booking.reject']);

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
    }
}
