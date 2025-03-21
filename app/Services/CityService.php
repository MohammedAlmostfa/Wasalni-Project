<?php

namespace App\Services;

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
            // Get the authenticated user's ID
            $id = Auth::user()->id;

            // Find the user by ID
            $user = User::findorfail($id);

            // Check if the user exists
            if (!$user) {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => ['User not found.'],
                    ],
                ];
            }

            // Access cities through the user's profile and country
            $cities = $user->cities;

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
