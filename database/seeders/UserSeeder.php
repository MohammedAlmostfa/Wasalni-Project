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
     * This function creates 10 random users with random roles (Admin, User, or PrivateUser).
     * Each user is assigned random profile images and random car images.
     * It uses the Faker library to generate fake user data and assigns predefined images from sample arrays.
     */
    public function run(): void
    {
        // Sample images that will be associated with users (profile and car images)
        // Each image has a name, path, mime type, and tag (profile or car)
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

        // Car images to be randomly assigned to users
        $carimages = [
            [
                'image_name' => '850aca7cd77c110e99ab20862aef14cf', // Image file name
                'image_path' => '://i.pinimg.com/736x/85/0a/ca/', // Image URL path
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
                'image_name' => '86ef174a63ad0d87b2c30ea2d8f583c7',
                'image_path' => '//i.pinimg.com/736x/86/ef/17/',
                'mime_type' => 'jpg',
                'tage' => 'car',
            ],
            [
                'image_name' => 'aeaebd758c62833c7ccf1ef08a60a58f',
                'image_path' => '//i.pinimg.com/736x/ae/ae/bd/',
                'mime_type' => 'jpg',
                'tage' => 'car',
            ],
            [
                'image_name' => '1e40ee22acdf96057cc657cf2aa88219',
                'image_path' => '//i.pinimg.com/736x/1e/40/ee/',
                'mime_type' => 'jpg',
                'tage' => 'car',
            ],
            [
                'image_name' => '081d0d512e925440f2f9d6259983eab5',
                'image_path' => '//i.pinimg.com/736x/08/1d/0d/',
                'mime_type' => 'jpg',
                'tage' => 'car',
            ]
        ];

        // Create a Faker instance to generate random fake data for the users
        $faker = Faker::create();

        // Loop to create 10 users in the database
        for ($i = 0; $i < 10; $i++) {
            // Create a new user with a unique email and a default hashed password
            $user = User::create([
                'email' => $faker->unique()->safeEmail(), // Generate a unique safe email
                'password' => bcrypt('P@ssw0rd123'), // Set a default hashed password
            ]);

            // Assign a random role to the user (Admin, User, or PrivateUser)
            $roleName = $faker->randomElement(['Admin', 'User', 'PrivateUser']); // Randomly choose a role
            $user->assignRole($roleName); // Assign the chosen role to the user

            // Retrieve the role that was assigned to the user
            $assignedRole = $user->roles()->where('name', $roleName)->first(); // Fetch the assigned role

            // Randomly choose one profile image and one car image from the predefined sample arrays
            $userImage = $userimages[array_rand($userimages)];
            $carImage = $carimages[array_rand($carimages)];

            // Associate both the profile and car images with the user
            $user->image()->create($userImage);
            $user->image()->create($carImage);

        }
    }
}
