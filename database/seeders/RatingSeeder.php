<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i < 100; $i++) {
            Rating::create([
                'user_id' => $faker->numberBetween(1, 10),
                'booking_id'=>$i,
                'rate'=>$faker->numberBetween(1, 5),
                'review'=> $faker->sentence,
            ]);
        }
    }
}
