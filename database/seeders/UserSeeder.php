<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $user= User::create([

                 'email' => $faker->unique()->email(),
                 'password' => bcrypt('P@ssw0rd123'),

             ]);
            $roleName = $faker->randomElement(['Admin', 'User', 'PrivateUser']);
            $user->assignRole($roleName);


            $assignedRole = $user->roles()->where('name', $roleName)->first();

            $user->roles()->updateExistingPivot($assignedRole->id, [
                'about_User' => 'This user is a private user',
                'car_Type' => 'SUV',
                'image_name' => 'ceb457975b293aa4bd9fd5d7a0dff8a2',
                'image_path' => '//i.pinimg.com/736x/ce/b4/57',
                'mime_type' => 'jpg',
            ]);



        }
    }
}
