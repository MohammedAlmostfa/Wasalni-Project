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
            $user->assignRole($faker->randomElement(['Admin', 'User','PrivateUser']));
        }
    }
}
