<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\RatingSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
                        CitySeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            //    CountrySeeder::class,

            ProfileSeeder::class,
            CitySeeder::class,
            TripSeeder::class,
            BookingSeeder::class,
            RatingSeeder::class,
        ]);
    }
}
