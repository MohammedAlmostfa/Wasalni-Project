<?php
namespace App\Policies;

use App\Models\Booking;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RatingPolicy
{
    /**
     * Determine whether the user can create a rating.
     */
    public function createRating(User $user, Booking $booking)
    {

        $rating = $user->ratings();
        if($rating) {
            return Response::deny(__('rating.rating_created_befor'), 403);
        }
        if ($user->id != $booking->user_id) {
            return Response::deny(__('rating.not_allowed_to_create_rating'), 403); // Forbidden
        }

        $trip = $booking->trip;
        if ($trip->status != "Ending") {
            return Response::deny(__('rating.trip_not_ending'), 400); // Bad Request
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the rating.
     */
    public function update(User $user, Rating $rating)
    {
        // Check if the user owns the rating
        if ($user->id != $rating->user_id) {
            return Response::deny(__('rating.not_allowed_to_update_rating'), 403);
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the rating.
     */
    public function delete(User $user, Rating $rating)
    {
        // Check if the user owns the rating
        if ($user->id != $rating->user_id) {
            return Response::deny(__('rating.not_allowed_to_delete_rating'), 403);
        }

        return Response::allow();
    }
}
