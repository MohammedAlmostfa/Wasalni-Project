<?php

namespace App\Services;

use Exception;
use App\Models\FavoritePerson;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FavoritePersonService
{
    /**
     * Retrieves a paginated list of all favorite users for the authenticated user.
     *
     * The method loads the relationship `favorites` and includes the associated profile information.
     *
     * @return array Contains a message, data (paginated list of favorite users), and status.
     */
    public function showFavoriteUsers()
    {
        try {
            /** @var \App\Models\User $user Authenticated user */
            $user = Auth::user();

            // Fetch favorite users along with their profile data
            $favorite_users = $user->favorites()
                ->with(['profile' => function ($query) {
                    $query->select('id', 'user_id', 'first_name', 'last_name'); // Selecting relevant profile fields
                },
                   'roles' => function ($query) {
                       $query->select('roles.id', 'roles.name')->withPivot('image_name', 'mime_type', 'image_path');
                   }])
                ->paginate(10);

            // Return successful response
            return [
                'message' => __('user.favorite_users_retrieved'),
                'data' => $favorite_users,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error details
            Log::error('Error in showFavoriteUsers: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')],
                ],
            ];
        }
    }

    /**
     * Adds a user to the authenticated user's favorite list.
     *
     * Attaches the specified user ID to the favorites list in the pivot table.
     *
     * @param array $data Contains the favorite_user_id to be added.
     * @return array Success message and status.
     */
    public function addToFavorite($data)
    {
        try {
            /** @var \App\Models\User $user Authenticated user */
            $user = Auth::user();
            // Attach the user to the authenticated user's favorite list
            $user->favorites()->attach($data['favorite_user_id']);

            // Return success response
            return [
                'message' => __('user.user_added_to_favorite'),
                'data' => null,
                'status' => 201, // HTTP 201 indicates resource creation
            ];
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in addToFavorite: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')],
                ],
            ];
        }
    }

    /**
     * Removes a user from the authenticated user's favorite list.
     *
     * Detaches the specified user ID from the favorites list.
     *
     * @param array $data Contains the favorite_user_id to be removed.
     * @return array Success message and status.
     */
    public function removeFromFavorite($data)
    {
        try {
            /** @var \App\Models\User $user Authenticated user */
            $user = Auth::user();
            // Detach the user from favorites
            $user ->favorites()->detach($data['favorite_user_id']);

            // Return success response
            return [
                'message' => __('user.user_removed_from_favorite'),
                'status' => 200, // HTTP 200 indicates successful deletion
            ];
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in removeFromFavorite: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')],
                ],
            ];
        }
    }
}
