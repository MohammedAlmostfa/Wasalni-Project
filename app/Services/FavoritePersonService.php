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
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $favorite_users = $user
                ->favoritePeople()
                ->with('favoriteUser.profile')
                ->paginate(10);

            return [
                'message' => __('user.favorite_users_retrieved'),  // Use translation key
                'data' => $favorite_users,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showFavoritePerson: ' . $e->getMessage());

            // Return an error message and status using translation key
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')], // Use translation key
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
                        'errorDetails' => [__('user.cant_add_yourself_to_favorite')], // Use translation key
                    ],
                ];
            }
            // Add to favorite list
            $favorite = FavoritePerson::create([
                'user_id' => Auth::user()->id,
                'favorite_user_id' => $data['favorite_user_id'],
            ]);

            return [
                'message' => __('user.user_added_to_favorite'), // Use translation key
                'data' => null,
                'status' => 201,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in addToFavorite: ' . $e->getMessage());

            // Return an error message and status using translation key
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')], // Use translation key
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
                'message' => __('user.user_removed_from_favorite'), // Use translation key
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in removeFromFavorite: ' . $e->getMessage());

            // Return an error message and status using translation key
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')], // Use translation key
                ],
            ];
        }
    }
}
