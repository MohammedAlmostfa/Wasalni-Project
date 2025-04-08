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
     * This method retrieves a paginated list of all users in the authenticated user's favorite list.
     * It uses the `favoritePeople` relationship and includes the favorite user's profile data.
     *
     * @return array Contains message, data (paginated list of favorite persons), and status.
     */
    public function showFavoritePerson()
    {
        try {
            /** @var \App\Models\User $user Authenticated user instance */
            $user = Auth::user();

            // Retrieve a paginated list of favorite persons with their profiles
            $favorite_users = $user
                ->favoritePeople()
                ->with('favoriteUser.profile')
                ->paginate(10);

            // Return the data along with a success message and status
            return [
                'message' => __('user.favorite_users_retrieved'),  // Translation key for success message
                'data' => $favorite_users,                         // Paginated list of favorite persons
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error details in case of failure
            Log::error('Error in showFavoritePerson: ' . $e->getMessage());

            // Return an error message with status code 500
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')], // General error message
                ],
            ];
        }
    }

    /**
     * Add a user to the authenticated user's favorite list.
     *
     * This method attaches a user (by ID) to the authenticated user's favorites list in the `favorite_people` table.
     *
     * @param array $data Contains the user_id of the person to be added to the favorite list.
     * @return array Contains a success message, data (null in this case), and status.
     */
    public function addToFavorite($data)
    {
        try {
            // Add the user to the authenticated user's favorites list
            Auth::user()->favorites()->attach($data['favorite_user_id']);

            // Return success response
            return [
                'message' => __('user.user_added_to_favorite'), // Translation key for success message
                'data' => null,                                // No additional data needed
                'status' => 201,                               // HTTP status code for resource creation
            ];
        } catch (Exception $e) {
            // Log the error details in case of failure
            Log::error('Error in addToFavorite: ' . $e->getMessage());

            // Return an error message with status code 500
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')], // General error message
                ],
            ];
        }
    }

    /**
     * Remove a user from the authenticated user's favorite list.
     *
     * This method detaches a user (by ID) from the authenticated user's favorites list in the `favorite_people` table.
     *
     * @param array $data Contains the user_id of the person to be removed from the favorite list.
     * @return array Contains a success message and status.
     */
    public function removeFromFavorite($data)
    {
        try {
            // Remove the user from the authenticated user's favorites list
            Auth::user()->favorites()->detach($data['favorite_user_id']);

            // Return success response
            return [
                'message' => __('user.user_removed_from_favorite'), // Translation key for success message
                'status' => 200,                                   // HTTP status code for success
            ];
        } catch (Exception $e) {
            // Log the error details in case of failure
            Log::error('Error in removeFromFavorite: ' . $e->getMessage());

            // Return an error message with status code 500
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('user.general_error')], // General error message
                ],
            ];
        }
    }
}
