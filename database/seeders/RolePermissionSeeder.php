<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method will create roles, permissions, and users with assigned roles and permissions.
     */
    public function run()
    {
        // Create roles for the application
        // 'Admin' role - can have full access to the app
        $adminRole = Role::create(['name' => 'Admin', 'guard_name' => 'api']);

        // 'User' role - regular user with standard access
        $userRole = Role::create(['name' => 'User', 'guard_name' => 'api']);

        // 'PrivateUser' role - a specific type of user with additional permissions
        $privateUserRole = Role::create(['name' => 'PrivateUser', 'guard_name' => 'api']);

        // Create permissions related to trips
        // These permissions will be used to control access to specific trip-related actions
        $createTripPermission = Permission::create(['name' => 'trip.create', 'guard_name' => 'api']);
        $viewTripPermission = Permission::create(['name' => 'trip.list', 'guard_name' => 'api']);
        $updateTripPermission = Permission::create(['name' => 'trip.update', 'guard_name' => 'api']);
        $deleteTripPermission = Permission::create(['name' => 'trip.delete', 'guard_name' => 'api']);
        $endTripPermission = Permission::create(['name' => 'trip.ended', 'guard_name' => 'api']);

        // Create permissions related to bookings
        // These permissions will be used to control actions for booking management
        $showBookingPermission = Permission::create(['name' => 'booking.show', 'guard_name' => 'api']);
        $acceptBookingPermission = Permission::create(['name' => 'booking.accept', 'guard_name' => 'api']);
        $rejectBookingPermission = Permission::create(['name' => 'booking.reject', 'guard_name' => 'api']);
        $cancelBookingPermission = Permission::create(['name' => 'booking.cancel', 'guard_name' => 'api']);
        $updataRequestPermission = Permission::create(['name' => 'request.update', 'guard_name' => 'api']);



        // Assign permissions to the 'PrivateUser' role
        // This gives the 'PrivateUser' role the ability to perform the actions defined in the permissions above
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
        $adminRole->givePermissionTo([
         $updataRequestPermission,
        ]);
        // Define the image to be associated with users (profile picture)
        // This is the image data that will be assigned to the users in the seeder
        $image = [
            'image_name' => 'ceb457975b293aa4bd9fd5d7a0dff8a2', // Image file name
            'image_path' => '//i.pinimg.com/736x/ce/b4/57', // URL path of the image
            'mime_type' => 'jpg', // Image MIME type
            'tage' => 'profile', // Tag to distinguish this image as a profile picture
        ];

        // Create an admin user
        // This user is assigned the 'Admin' role and has access to all the permissions
        $adminUser = User::create([
            'email' => 'admin@example.com', // Admin email
            'password' => bcrypt('P@ssw0rd123'), // Admin password (hashed)
        ]);

        // Assign the 'Admin' role to this user
        $adminUser->assignRole($adminRole);

        // Update the pivot table for the 'Admin' role to add additional attributes (if required)
        // These attributes may represent additional information like 'about_User' and 'car_Type' for admin users
        $adminUser->roles()->updateExistingPivot($adminRole->id, [
            'about_User' => 'This user is a private user', // Custom field for admin
            'car_Type' => 'SUV', // Custom field for admin
        ]);

        // Associate the image with the admin user (profile image)
        $adminUser->image()->create($image);

        // Create a private user
        // This user is assigned the 'PrivateUser' role and has limited permissions
        $privateUser = User::create([
            'email' => 'private@example.com', // Private user email
            'password' => bcrypt('P@ssw0rd123'), // Private user password (hashed)
        ]);

        // Assign the 'PrivateUser' role to this user
        $privateUser->assignRole($privateUserRole);

        // Update the pivot table for the 'PrivateUser' role to add additional attributes
        $privateUser->roles()->updateExistingPivot($privateUserRole->id, [
            'about_User' => 'This user is a private user', // Custom field for private user
            'car_Type' => 'SUV', // Custom field for private user
        ]);

        // Associate the image with the private user (profile image)
        $privateUser->image()->create($image);
    }
}
