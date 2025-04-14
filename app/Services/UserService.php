<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
     * Fetch private users in the authenticated user's city.
     *
     * @return array Status, message, and data of matching users.
     */
    public function showUsers()
    {
        try {
            $user = Auth::user();

            // Validate the authenticated user's profile
            if (!$user || !$user->profile) {
                return [
                    'status' => 404,
                    'message' => [__('user.user_profile_not_found')],
                ];
            }

            $city_id = $user->profile->city_id;

            // Retrieve users with the same city and 'PrivateUser' role
            $users = User::whereHas('profile', function ($query) use ($city_id) {
                $query->where('city_id', $city_id);
            })
            ->whereHas('roles', function ($query) {
                $query->where('name', 'PrivateUser');
            })
            ->with('profile:id,user_id,first_name,last_name') // Load limited profile details
            ->select('id') // Select user IDs only
            ->get();

            return [
                'message' => __('user.private_user_retrieved'),
                'status' => 200,
                'data' => $users,
            ];
        } catch (Exception $e) {
            Log::error('Error in showUsers: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [__('user.general_error')],
            ];
        }
    }

    /**
     * Fetch detailed profile data for a specific user.
     *
     * @param User $user The user to retrieve data for.
     * @return array Status, message, and detailed user data.
     */
    public function showUser(User $user)
    {
        try {
            // Load user data with relationships and calculated fields
            $UserData = User::with([
                'profile' => function ($query) {
                    $query->select('user_id', 'first_name', 'last_name', 'birthday');
                },
              'tripproperies' => function ($query) {
                  $query->select('attributes'); // تحميل البيانات بشكل صحيح
              },

                'tripRatings' => function ($query) {
                    $query->select('ratings.id', 'ratings.rate', 'ratings.review', 'ratings.user_id', 'ratings.created_at')
                    ->with(['user.profile' => function ($query) {
                        $query->select('id', 'user_id', 'first_name', 'last_name', 'phone');
                    }]);
                },
                'roles' => function ($query) {
                    $query->select('roles.id', 'roles.name')->withPivot('about_User', 'car_Type', );
                },
'image',
            ])
            ->withCount('trips as User_trips_count') // Count the number of trips
            ->withAvg('tripRatings as avg_rating', 'rate') // Calculate average trip rating
            ->find($user->id);

            // Validate if user data exists
            if (!$UserData) {
                return [
                    'message' => __('user.user_profile_not_found_specified'),
                    'status' => 404,
                    'data' => null,
                ];
            }

            // Check if the authenticated user has marked this user as favorite
            $authenticatedUser = Auth::user();
            $isFavorite = $authenticatedUser->favorites()->where('favorite_user_id', $user->id)->exists();
            $UserData->is_favorite = $isFavorite;


            return [
                'message' => __('user.user_profile_retrieved'),
                'status' => 200,
                'data' => $UserData,
            ];
        } catch (Exception $e) {
            Log::error('Error in showUser: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [__('user.general_error')],
            ];
        }
    }
}
