<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
     * Retrieve private users based on the authenticated user's city.
     *
     * This method fetches users who:
     * - Have a profile with the same city_id as the authenticated user.
     * - Have the 'PrivateUser' role assigned.
     *
     * The response includes minimal user data (id) with associated profile names.
     *
     * @return array [
     *   'status' => int,    // HTTP status code (200, 404, or 500)
     *   'message' => mixed, // Success message or error details
     *   'data' => mixed     // Collection of users or null on error
     * ]
     */
    public function showUsers()
    {
        try {
            /** @var \App\Models\User $user */
            // Get the currently authenticated user
            $user = Auth::user();

            // Validate user and profile existence
            if (!$user || !$user->profile) {
                // Return error response if the user profile is not found
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => [__('user.user_profile_not_found')], // Translation for profile not found
                    ],
                ];
            }

            // Get the city_id from the user's profile
            $city_id = $user->profile->city_id;

            // Query users with matching city_id and the 'PrivateUser' role
            $users = User::whereHas('profile', function ($query) use ($city_id) {
                $query->where('city_id', $city_id);
            })
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'PrivateUser');
                })
                ->with('profile:id,user_id,first_name,last_name') // Include profile data (id, first_name, last_name)
                ->select('id') // Only select the user ID
                ->get();

            // Return successful response with retrieved users
            return [
                'message' => __('user.private_user_retrieved'), // Translation for success message
                'status' => 200,
                'data' => $users,
            ];
        } catch (Exception $e) {
            // Log the error message
            Log::error('Error in showUsers: ' . $e->getMessage());

            // Return error response if an exception occurs
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')], // Translation for general error
                ],
            ];
        }
    }

    /**
     * Retrieve detailed profile information for a specific user.
     *
     * This method fetches the following details for a user:
     * - Basic profile information (first_name, last_name, birthday)
     * - Trip ratings with associated user details
     * - User roles with pivot data (about_User, car_Type)
     * - Favorite status relative to the authenticated user
     * - Number of trips the user has
     *
     * @param User $user The user model to retrieve details for
     * @return array [
     *   'status' => int,    // HTTP status code (200, 404, or 500)
     *   'message' => mixed, // Success message or error details
     *   'data' => mixed     // User data with relationships or null on error
     * ]
     */
    public function showUser(User $user)
    {
        try {
            // Load user data with relationships:
            // - Profile with selected columns (user_id, first_name, last_name, birthday)
            // - Trip ratings with nested user profiles (user_id, rate, review)
            // - Roles with pivot data (about_User, car_Type)
            $UserData = User::with([
                'profile' => function ($query) {
                    $query->select('user_id', 'first_name', 'last_name', 'birthday');
                },
                'tripRatings' => function ($query) {
                    $query->select(
                        'ratings.id',
                        'ratings.rate',
                        'ratings.review',
                        'ratings.user_id',
                        'ratings.created_at'
                    )
                    ->with(['user.profile' => function ($query) {
                        $query->select('id', 'user_id', 'first_name', 'last_name', 'phone');
                    }]);
                },
                'roles' => function ($query) {
                    $query->select('roles.id', 'roles.name')
                          ->withPivot('about_User', 'car_Type');
                }
            ])->find($user->id);

            // Check if the user data was found
            if (!$UserData) {
                // Return error response if user profile is not found
                return [
                    "message" => __('user.user_profile_not_found_specified'), // Translation for profile not found
                    'status' => 404,
                    'data' => null,
                ];
            }

            /** @var \App\Models\User $user */

            $authenticatedUser = Auth::user();

            // Check if the authenticated user has this user in their favorites
            $isFavorite = $authenticatedUser->favoritePeople()->where('favorite_user_id', $user->id)->exists();

            // Get the number of trips the user has
            $Usertrips = $user->trips()->count();

            // Add favorite status and trip count to the user data
            $UserData->is_favorite = $isFavorite;
            $UserData->User_trips_count = $Usertrips;

            // Return successful response with user data
            return [
                "message" => __('user.user_profile_retrieved'), // Translation for profile retrieved successfully
                'status' => 200,
                'data' => $UserData,
            ];
        } catch (Exception $e) {
            // Log the error message
            Log::error('Error in showUser: ' . $e->getMessage());

            // Return error response if an exception occurs
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')], // Translation for general error
                ],
            ];
        }
    }
}
