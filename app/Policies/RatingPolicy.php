<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RatingPolicy
{
    /**
     * Determine whether the user can create a rating for a booking.
     *
     * @param User $user The authenticated user.
     * @param Booking $booking The booking for which the rating is being created.
     * @return Response Allow if the user is allowed to create a rating; otherwise, deny with a message.
     */
    public function createRating(User $user, Booking $booking)
    {
        // Check if a rating already exists for this booking
        $rating = $booking->rating()->first();
        if ($rating) {
            return Response::deny(__('rating.rating_created_before'), 403); // Forbidden
        }

        // Check if the user is the owner of the booking
        if ($user->id != $booking->user_id) {
            return Response::deny(__('rating.not_allowed_to_create_rating'), 403); // Forbidden
        }

        // Check if the trip associated with the booking is in the "Ending" status
        $trip = $booking->trip;
        if ($trip->status != "Ending") {
            return Response::deny(__('rating.trip_not_ending'), 400); // Bad Request
        }

        // Allow the user to create a rating if all conditions are met
        return Response::allow();
    }

    /**
     * Determine whether the user can update a rating.
     *
     * @param User $user The authenticated user.
     * @param Rating $rating The rating to be updated.
     * @return Response Allow if the user is allowed to update the rating; otherwise, deny with a message.
     */
    public function update(User $user, Rating $rating)
    {
        // Check if the user is the owner of the rating
        if ($user->id != $rating->user_id) {
            return Response::deny(__('rating.not_allowed_to_update_rating'), 403); // Forbidden
        }

        // Allow the user to update the rating
        return Response::allow();
    }

    /**
     * Determine whether the user can delete a rating.
     *
     * @param User $user The authenticated user.
     * @param Rating $rating The rating to be deleted.
     * @return Response Allow if the user is allowed to delete the rating; otherwise, deny with a message.
     */
    public function delete(User $user, Rating $rating)
    {
        // Check if the user is the owner of the rating
        if ($user->id != $rating->user_id) {
            return Response::deny(__('rating.not_allowed_to_delete_rating'), 403); // Forbidden
        }

        // Allow the user to delete the rating
        return Response::allow();
    }
}
