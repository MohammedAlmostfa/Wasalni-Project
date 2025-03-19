<?php

namespace App\Services;

use Exception;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class CountryService
{
    /**
     * Retrieve all countries.
     *
     * This method fetches all countries from the database and returns them in a structured response.
     *
     * @return array Contains message, data (list of countries), and status.
     */
    public function getCountries()
    {
        try {

            // Fetch all countries from the database
            $countries = Country::all();

            // Return the list of countries with a success message
            return [
                'message' => 'Countries fetched successfully',
                'data' => $countries,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in getCountries: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while fetching countries.'],
                ],
            ];
        }
    }
}
