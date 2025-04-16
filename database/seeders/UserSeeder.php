<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This function creates 10 random users with random roles and assigns them random profile images.
     * Each user is also assigned a random role (Admin, User, or PrivateUser).
     */
    public function run(): void
    {
        // Sample images that will be associated with users
        // Each image has a name, path, mime type, and tag (in this case, 'profile')
        $userimages = [
            [
                'image_name' => 'ceb457975b293aa4bd9fd5d7a0dff8a2', // Image file name
                'image_path' => '//i.pinimg.com/736x/ce/b4/57', // Image URL path
                'mime_type' => 'jpg', // MIME type for the image
                'tage' => 'profile', // Tag to categorize image type
            ],
            [
                'image_name' => 'b82ef23538493d6911a5ad7d0d0c4f2e',
                'image_path' => '//i.pinimg.com/736x/b8/2e/f2/',
                'mime_type' => 'jpg',
                'tage' => 'profile',
            ],
            [
                'image_name' => 'cb02dbc732e27b8135ef5b65a81c8e45',
                'image_path' => '//i.pinimg.com/736x/cb/02/db/',
                'mime_type' => 'jpg',
                'tage' => 'profile',
            ],
            [
                'image_name' => 'd34337ea390f116247c3cd4719abdbd2',
                'image_path' => '//i.pinimg.com/474x/d3/43/37/',
                'mime_type' => 'jpg',
                'tage' => 'profile',
            ],
        ];
        $carimages = [
           [
               'image_name' => 'ceb457975b293aa4bd9fd5d7a0dff8a2', // Image file name
               'image_path' => '//i.pinimg.com/736x/ce/b4/57', // Image URL path
               'mime_type' => 'jpg', // MIME type for the image
               'tage' => 'car', // Tag to categorize image type
           ],
           [
               'image_name' => 'b82ef23538493d6911a5ad7d0d0c4f2e',
               'image_path' => '//i.pinimg.com/736x/b8/2e/f2/',
               'mime_type' => 'jpg',
               'tage' => 'car',
           ],
           [
               'image_name' => 'cb02dbc732e27b8135ef5b65a81c8e45',
               'image_path' => '//i.pinimg.com/736x/cb/02/db/',
               'mime_type' => 'jpg',
               'tage' => 'car',
           ],
           [
               'image_name' => 'd34337ea390f116247c3cd4719abdbd2',
               'image_path' => '//i.pinimg.com/474x/d3/43/37/',
               'mime_type' => 'jpg',
               'tage' => 'car',
           ],
       ];


        // Faker instance to generate fake data for users
        $faker = Faker::create();

        // Loop to create 10 users in the database
        for ($i = 0; $i < 10; $i++) {
            // Create a new user with a unique email and a hashed password
            $user = User::create([
                'email' => $faker->unique()->safeEmail(), // Generate a unique safe email
                'password' => bcrypt('P@ssw0rd123'), // Set a default hashed password
            ]);

            // Assign a random role to the user from the options: Admin, User, PrivateUser
            $roleName = $faker->randomElement(['Admin', 'User', 'PrivateUser']); // Randomly choose a role
            $user->assignRole($roleName); // Assign the chosen role to the user

            // Retrieve the role that was assigned to the user
            $assignedRole = $user->roles()->where('name', $roleName)->first(); // Fetch the assigned role

            // Randomly choose an image from the available sample images
            // Randomly choose one profile image and one car image
            $userImage = $userimages[array_rand($userimages)];
            $carImage = $carimages[array_rand($carimages)];

            // Associate both images with the user
            $user->image()->create($userImage);
            $user->image()->create($carImage);

        }
    }
}
