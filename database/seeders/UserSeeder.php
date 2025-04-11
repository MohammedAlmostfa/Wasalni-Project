<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = [
            [
                'image_name' => 'ceb457975b293aa4bd9fd5d7a0dff8a2',
                'image_path' => '//i.pinimg.com/736x/ce/b4/57',
                'mime_type' => 'jpg',
            ],
            [
                'image_name' => 'b82ef23538493d6911a5ad7d0d0c4f2e',
                'image_path' => '//i.pinimg.com/736x/b8/2e/f2/',
                'mime_type' => 'jpg',
            ],
            [
                'image_name' => 'cb02dbc732e27b8135ef5b65a81c8e45',
                'image_path' => '//i.pinimg.com/736x/cb/02/db/',
                'mime_type' => 'jpg',
            ],
            [
                'image_name' => 'd34337ea390f116247c3cd4719abdbd2',
                'image_path' => '//i.pinimg.com/474x/d3/43/37/',
                'mime_type' => 'jpg',
            ],
        ];

        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $user = User::create([
                'email' => $faker->unique()->safeEmail(),
                'password' => bcrypt('P@ssw0rd123'),
            ]);

            $roleName = $faker->randomElement(['Admin', 'User', 'PrivateUser']);
            $user->assignRole($roleName);

            $assignedRole = $user->roles()->where('name', $roleName)->first();


            $image = $images[array_rand($images)];

            if ($assignedRole && $roleName !== 'User') {
                $user->roles()->updateExistingPivot($assignedRole->id, [
                    'about_User' => 'This user is a private user',
                    'car_Type' => 'SUV',
                    'image_name' => $image['image_name'],
                    'image_path' => $image['image_path'],
                    'mime_type' => $image['mime_type'],
                ]);
            }
        }
    }
}
