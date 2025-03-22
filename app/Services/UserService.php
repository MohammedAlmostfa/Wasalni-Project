<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
 * Retrieve users based on the logged-in user's city and role.
 *
 * This method fetches users who:
 * - Have a profile with the same `city_id` as the logged-in user.
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

            // Retrieve the city of the logged-in user
            $city_id = $user->profile->city_id;

            // Fetch users based on the city and role
            $users = User::whereHas('profile', function ($query) use ($city_id) {
                $query->where('city_id', $city_id); // Filter by city_id in the profile
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

    /**
     * Retrieve profile details of a specific user.
     *
     * This method fetches the profile details of a user, including:
     * - First name
     * - Last name
     * - Gender
     * - Phone number
     * - Address
     * - Country name
     *
     * @param User $user The user whose profile details are to be retrieved.
     * @return array
     *   - If successful: Returns a success message, status code 200, and the profile details.
     *   - If the profile is not found: Returns a 404 error.
     *   - If an exception occurs: Returns a 500 error with a generic error message.
     */
    public function showUser(User $user)
    {
        try {
            // Fetch profile details of the user
            $usedata = User::select(
                'profiles.first_name',
                'profiles.last_name',
                'profiles.gender',
                'profiles.phone',
                'profiles.address',
                'countries.country_name as country_name'
            )
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->join('countries', 'profiles.country_id', '=', 'countries.id')
            ->where('users.id', $user->id)
            ->first();

            // Check if the profile exists
            if (!$usedata) {
                return [
                    "message" => 'User profile not found for the specified user.',
                    'status' => 404,
                    'data' => null,
                ];
            }

            // Return success response with the retrieved profile details
            return [
                "message" => 'User profile retrieved successfully',
                'status' => 200,
                'data' => $usedata,
            ];
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error in showUser: ' . $e->getMessage());

            // Return a generic error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while retrieving user profile.'],
                ],
            ];
        }
    }
}
