<?php

namespace App\Services;

use Exception;
use App\Models\FavoritePerson;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FavoritePersonService
{
    /**
     * Show all favorite persons for the authenticated user.
     *
     * @return array Contains message, data (paginated list of favorite persons), and status.
     */
    public function showFavoritePerson()
    {
        try {
            // Get the authenticated user's favorite persons with pagination
            $favorite_users = Auth::user()
                ->favoritePeople()
                ->with('favoriteUser.profile')
                ->paginate(10);

            return [
                'message' => 'All favorite users retrieved successfully.',
                'data' => $favorite_users,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showFavoritePerson: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while retrieving favorite users.'],
                ],
            ];
        }
    }

    /**
     * Add a user to the authenticated user's favorite list.
     *
     * @param array $data Contains the user_id of the person to be added.
     * @return array Contains message, data (created favorite record), and status.
     */
    public function addToFavorite($data)
    {
        try {
            // Check if the user is trying to add themselves
            if (Auth::user()->id == $data['favorite_user_id']) {
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => ['You cannot add yourself to your favorite list.'],
                    ],
                ];
            }
            // Add to favorite list
            $favorite = FavoritePerson::create([
                'user_id' => Auth::user()->id,
                'favorite_user_id' => $data['favorite_user_id'],
            ]);

            return [
                'message' => 'User added to your favorite users.',
                'data' => null,
                'status' => 201,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in addToFavorite: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while adding the user to your favorite list.'],
                ],
            ];
        }
    }

    /**
     * Remove a user from the authenticated user's favorite list.
     *
     * @param FavoritePerson $favoritePerson The favorite record to be deleted.
     * @return array Contains message and status.
     */
    public function removeFromFavorite($favoritePerson)
    {
        try {
            // Remove from favorite list
            $favoritePerson->delete();

            return [
                'message' => 'User removed from your favorite list.',
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in removeFromFavorite: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while removing the user from your favorite list.'],
                ],
            ];
        }
    }
}
