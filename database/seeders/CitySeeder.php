<?php

namespace Database\Seeders;

use App\Models\City;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CitySeeder extends Seeder
{
    public function run()
    {
        $countryName = 'Iraq';
        // Make API call to fetch cities for the selected country
        $response = Http::post('https://countriesnow.space/api/v0.1/countries/cities', [
             'country' => $countryName,
         ]);


        // If the API call is successful, save the cities to the database
        if ($response->successful()) {
            $cities = $response->json()['data']; // Extract cities from the response
            {
                // Create cities in the database
                foreach ($cities as $cityName) {
                    City::create([
                        'city_name' => $cityName,
                     //   'country_id' => $country->id,
                    ]);
                }
            }
        }

    }
}
