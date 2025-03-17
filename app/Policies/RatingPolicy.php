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
    public function createRating(User $user, $booking_id)
    {
        $booking = Booking::find($booking_id);

        // Check if the booking exists and the user owns it
        if (!$booking || $user->id != $booking->user_id) {
            return Response::deny('You are not allowed to create a rating for this booking.', 403); // Forbidden
        }

        // Check if the trip associated with the booking has ended
        $trip = $booking->trip;
        if ($trip->status != "ending") {
            return Response::deny('The trip is not ending.', 400); // Bad Request
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
            return Response::deny('You are not allowed to update this rating.', 403);
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
            return Response::deny('You are not allowed to delete this rating.', 403);
        }

        return Response::allow();
    }
}
