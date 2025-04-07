<?php

namespace Database\Seeders;

use App\Models\Booking;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 100; $i++) {
            Booking::create([
                "trip_id" => $faker->numberBetween(1, 9),
                "status" => $faker->randomElement(['pending', 'accepted', 'rejected', 'cancel']),
                "seats_number" => $faker->numberBetween(1, 9),
                "user_id" => $faker->numberBetween(1, 9),
                'nots' => $faker->sentence,
            ]);
        }
    }
}
