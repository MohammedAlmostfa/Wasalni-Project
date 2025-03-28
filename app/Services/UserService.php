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
     * - Have a profile with the same city_id as the authenticated user
     * - Have the 'PrivateUser' role assigned
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
            // Get the currently authenticated user
            $user = Auth::user();

            // Validate user and profile existence
            if (!$user || !$user->profile) {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => ['User profile not found.'],
                    ],
                ];
            }

            // Get the city_id from the user's profile
            $city_id = $user->profile->city_id;

            // Query users with:
            // - Matching city_id in their profile
            // - 'PrivateUser' role
            $users = User::whereHas('profile', function ($query) use ($city_id) {
                $query->where('city_id', $city_id);
            })
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'PrivateUser');
                })
                ->with('profile:id,user_id,first_name,last_name')
                ->select('id')
                ->get();

            return [
                "message" => 'PrivateUser retrieved successfully',
                'status' => 200,
                'data' => $users,
            ];
        } catch (Exception $e) {
            Log::error('Error in showUsers: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while retrieving users.'],
                ],
            ];
        }
    }

    /**
     * Retrieve detailed profile information for a specific user.
     *
     * Includes:
     * - Basic profile information (first_name, last_name, birthday)
     * - Trip ratings with associated user details
     * - User roles with pivot data (about_User, car_Type)
     * - Favorite status relative to the authenticated user
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
            // - Profile with selected columns
            // - Trip ratings with nested user profiles
            // - Roles with pivot data
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

            // Check if user data was found
            if (!$UserData) {
                return [
                    "message" => 'User profile not found for the specified user.',
                    'status' => 404,
                    'data' => null,
                ];
            }
            // Get the authenticated user
            $authenticatedUser = Auth::user();

            // Check if the authenticated user has this user in their favorites
            $isFavorite = $authenticatedUser->favoritePeople()->where('favorite_user_id', $user->id)->exists();

            $Usertrips=$user->trips()->count();
            // Add favorite status to the user data
            $UserData->is_favorite = $isFavorite;
            $UserData->User_trips_count=$Usertrips;

            return [
                "message" => 'User profile retrieved successfully',
                'status' => 200,
                'data' => $UserData,
            ];
        } catch (Exception $e) {
            Log::error('Error in showUser: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while retrieving user profile.'],
                ],
            ];
        }
    }
}
