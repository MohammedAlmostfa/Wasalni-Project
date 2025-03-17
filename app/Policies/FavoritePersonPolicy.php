<?php
namespace App\Policies;

use App\Models\FavoritePerson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FavoritePersonPolicy
{
    /**
     * Determine if the user can add a favorite person.
     */
    public function addfavorite(User $user, $favoriteUser_id)
    {
        if ($user->id == $favoriteUser_id) {
            return Response::deny('You cannot add yourself to your favorite list.', 400);
        }

        return Response::allow();
    }

    /**
     * Determine if the user can remove a favorite person.
     */
    public function delete(User $user, FavoritePerson $favoritePerson)
    {
        if ($user->id != $favoritePerson->user_id) {
            return Response::deny('You are not authorized to remove this favorite person.', 403);
        }

        return Response::allow();
    }
}
