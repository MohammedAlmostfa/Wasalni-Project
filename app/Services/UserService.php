<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
     * Retrieve users based on the logged-in user's country and role.
     *
     * This method fetches users who:
     * - Have a profile with the same `country_id` as the logged-in user.
     * - Have the role `PrivateUser`.
     *
     * @return array
     *   - If successful: Returns a success message, status code 200, and the list of users.
     *   - If the logged-in user or their profile is not found: Returns a 404 error.
     *   - If an exception occurs: Returns a 500 error with a generic error message.
     */
    public function showUsers()
    {
        try {
            // Retrieve the logged-in user
            $user = Auth::user();

            // Check if the user has a profile
            if (!$user || !$user->profile) {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => ['User profile not found.'],
                    ],
                ];
            }

            // Retrieve the country_id of the logged-in user
            $country_id = $user->profile->country_id;

            // Fetch users based on the country_id and role
            $users = User::whereHas('profile', function ($query) use ($country_id) {
                $query->where('country_id', $country_id); // Filter by country_id in the profile
            })->whereHas('roles', function ($query) {
                $query->where('name', 'PrivateUser'); // Filter by role name
            })->with('profile:id,user_id,first_name,last_name') // Eager load profile with specific fields
              ->select('id') // Select only the user ID
              ->get();

            // Return success response with the retrieved users
            return [
                "message" => 'PrivateUser retrieved successfully',
                'status' => 200,
                'data' => $users,
            ];
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error in showUsers: ' . $e->getMessage());

            // Return a generic error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while retrieving users.'],
                ],
            ];
        }
    }


}
