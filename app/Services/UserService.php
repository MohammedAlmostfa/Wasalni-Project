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
     * @return array [
     *   'status' => int,    // HTTP status code
     *   'message' => mixed, // Response message or error details
     *   'data' => mixed     // Retrieved data or null on error
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
     * - Basic profile information
     * - Trip ratings with associated user details
     *
     * @param User $user The user model to retrieve details for
     * @return array [
     *   'status' => int,    // HTTP status code
     *   'message' => mixed, // Response message or error details
     *   'data' => mixed     // User data or null on error
     * ]
     */
    public function showUser(User $user)
    {
        try {
            $ratings = User::with([
                // Load profile with selected columns
                'profile' => function ($query) {
                    $query->select('user_id', 'first_name', 'last_name', 'birthday');
                },
                // Load trip ratings with selected columns and nested relationships
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
                // Load roles with aboutuser and cartype from the pivot table
                'roles' => function ($query) {
                    $query->select('roles.id', 'roles.name') // تحديد الأعمدة المطلوبة من جدول الأدوار
                          ->withPivot('about_User', 'car_Type'); // إضافة الأعمدة من جدول الـ pivot
                }
            ])->find($user->id);

            // Validate data existence
            if (!$ratings) {
                return [
                    "message" => 'User profile not found for the specified user.',
                    'status' => 404,
                    'data' => null,
                ];
            }

            return [
                "message" => 'User profile retrieved successfully',
                'status' => 200,
                'data' => $ratings,
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
