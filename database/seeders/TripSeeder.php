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
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $groupDate = null;
        $fixedFromCity = 3; // المدينة الثابتة للرحلة الأولى في كل مجموعة

        for ($i = 0; $i < 30; $i++) {
            // كل 3 رحلات نغير التاريخ
            if ($i % 3 === 0) {
                $groupDate = $faker->dateTimeBetween('now', '+2 months')->format('Y-m-d H:00');
            }

            $seatPrices = range(1500, 3000, 100);
            $seatPrice = $seatPrices[array_rand($seatPrices)];

            // تحديد مدينة المغادرة (أول رحلة في المجموعة من مدينة 3، الباقي عشوائي)
            $fromCity = ($i % 3 === 0) ? $fixedFromCity : $faker->numberBetween(1, 9);

            // التأكد أن المدينة الوجهة مختلفة عن مدينة المغادرة
            do {
                $toCity = $faker->numberBetween(1, 9);
            } while ($toCity == $fromCity);

            Trip::create([
                'description' => $faker->sentence,
                'trip_start' => $groupDate,
                'from' => $fromCity,
                'to' => $toCity,
                'status' => $faker->randomElement(['Pending', 'Ongoing', 'Completed']),
                'seat_price' => $seatPrice,
                'available_seats' => $faker->numberBetween(1, 20),
                'user_id' => $faker->numberBetween(1, 2),
            ]);
        }
    }
}
