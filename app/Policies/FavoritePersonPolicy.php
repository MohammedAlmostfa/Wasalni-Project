<?php

namespace App\Policies;

use App\Models\FavoritePerson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Class FavoritePersonPolicy
 *
 * This class handles authorization logic for actions related to the FavoritePerson model.
 * It determines whether a user is allowed to perform specific actions, such as adding or removing a favorite person.
 */
class FavoritePersonPolicy
{
    /**
     * Determine if the user can add a favorite person.
     *
     * @param User $user The authenticated user attempting to add a favorite.
     * @param int $favoriteUser_id The ID of the user being added as a favorite.
     * @return Response
     */
    public function addfavorite(User $user, $favoriteUser_id)
    {
        // Prevent users from adding themselves to their favorite list
        if ($user->id == $favoriteUser_id) {
            return Response::deny('You cannot add yourself to your favorite list.', 400);
        }

        // Allow the action if the user is not adding themselves
        return Response::allow();
    }

    /**
     * Determine if the user can remove a favorite person.
     *
     * @param User $user The authenticated user attempting to remove a favorite.
     * @param FavoritePerson $favoritePerson The favorite person relationship being deleted.
     * @return Response
     */
    public function delete(User $user, FavoritePerson $favoritePerson)
    {
        // Ensure the user can only remove their own favorite relationships
        if ($user->id != $favoritePerson->user_id) {
            return Response::deny('You are not authorized to remove this favorite person.', 403);
        }

        // Allow the action if the user is the owner of the favorite relationship
        return Response::allow();
    }
}
