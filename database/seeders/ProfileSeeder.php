<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Profile; // Ensure you import the Profile model

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i < 11; $i++) {
            Profile::create([
                'user_id' => $i, // Assuming user_id is a foreign key
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'gender' => $faker->randomElement(['male', 'female']),
                'birthday' => $faker->date,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'city_id' => $faker->numberBetween(1, 10),
            ]);
        }
    }
}
