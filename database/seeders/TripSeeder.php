<?php

namespace Database\Seeders;

use App\Models\Trip;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 3; $i++) {
            $seatPrices = range(1500, 3000, 100);
            $seatPrice = $seatPrices[array_rand($seatPrices)];

            Trip::create([
                'description' => $faker->sentence,
                'trip_start' => $faker->dateTimeBetween('now', '+2 months')->format('Y-m-d H:00'),
                'from' => $faker->numberBetween(1, 9),
                'to' => $faker->numberBetween(1, 9),
                'status' => $faker->randomElement(['Pending', 'Ending', 'Complete']),
                'seat_price' => $seatPrice,
                'available_seats' => $faker->numberBetween(1, 20),
                'user_id' => $faker->numberBetween(1, 2),
            ]);
        }

    }
}
