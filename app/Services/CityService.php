<?php

namespace App\Services;

use App\Models\City;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CityService
{
    /**
     * Retrieve cities associated with the authenticated user.
     *
     * This method retrieves the cities linked to the authenticated user through their profile and country.
     *
     * @return array Contains message, status, and data (list of cities).
     */
    public function getCities()
    {
        try {


            //get cities from database
            $cities = City::select('city_name', 'id')->get();

            // Return the list of cities with a success message
            return [
                'message' => 'Cities retrieved successfully',
                'status' => 200,
                'data' => $cities,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in getCities: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while retrieving cities.'],
                ],
            ];
        }
    }
}
