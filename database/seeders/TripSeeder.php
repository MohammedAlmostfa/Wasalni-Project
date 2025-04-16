<?php

namespace Database\Seeders;

use App\Models\Trip;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method will create 30 trips with random data for the trips' attributes such as
     * description, start date, seat prices, and available seats.
     *
     * @return void
     */
    public function run()
    {
        // Create a Faker instance to generate fake data
        $faker = Faker::create();

        // Variable to store a common date for a group of trips (every 3 trips share the same date)
        $groupDate = null;

        // Fixed departure city for the first trip in each group of 3 trips
        $fixedFromCity = 3;

        // Loop to create 30 trips
        for ($i = 0; $i < 30; $i++) {

            // Every 3rd trip in the loop will have the same start date
            if ($i % 3 === 0) {
                // Generate a random future date between now and 2 months from now
                $groupDate = $faker->dateTimeBetween('now', '+2 months')->format('Y-m-d H:00');
            }

            // Generate a random seat price between 1500 and 3000 with a step of 100
            $seatPrices = range(1500, 3000, 100);
            $seatPrice = $seatPrices[array_rand($seatPrices)];

            // Determine the departure city
            // For the first trip in every group of 3 trips, set a fixed departure city (City 3)
            // For all other trips, choose a random city between 1 and 9
            $fromCity = ($i % 3 === 0) ? $fixedFromCity : $faker->numberBetween(1, 9);

            // Ensure that the destination city is different from the departure city
            do {
                $toCity = $faker->numberBetween(1, 9);
            } while ($toCity == $fromCity); // Keep generating a new destination city until it's not the same as departure city

            // Create a new Trip in the database with the generated data
            Trip::create([
                'description' => $faker->sentence, // Random description for the trip
                'trip_start' => $groupDate, // Random start date, shared across 3 trips
                'from' => $fromCity, // Departure city
                'to' => $toCity, // Destination city
                'status' => $faker->randomElement(['Pending', 'Ongoing', 'Completed']), // Random trip status
                'seat_price' => $seatPrice, // Random seat price from generated prices
                'available_seats' => $faker->numberBetween(1, 20), // Random number of available seats between 1 and 20
                'user_id' => $faker->numberBetween(1, 2), // Assign the trip to a user (either user 1 or 2)
            ]);
        }
    }
}
