<?php
namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can update the booking.
     */
    public function update(User $user, Booking $booking)
    {
        if ($booking->user_id == $user->id && $booking->status == "pending") {
            return Response::allow();
        }

        return Response::deny('You are not allowed to update this booking.', 403); // Forbidden
    }

    /**
     * Determine whether the user can delete the booking.
     */
    public function delete(User $user, Booking $booking)
    {
        if ($booking->user_id == $user->id && $booking->status == "pending") {
            return Response::allow();
        }

        return Response::deny('You are not allowed to delete this booking.', 403); // Forbidden
    }

    /**
     * Determine whether the user can restore the booking.
     */
    public function restore(User $user, Booking $booking)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can accept the booking.
     */
    public function accept(User $user, Booking $booking)
    {
        // Condition 1: The user must be the owner of the trip
        if ($booking->trip->user_id != $user->id) {
            return Response::deny('You are not allowed to accept this booking.', 403); // Forbidden
        }

        // Condition 2: The booking must be in "pending" or "accepted" status
        if ($booking->status != "pending" && $booking->status != "accepted") {
            return Response::deny('The booking is not in a valid status for acceptance.', 400); // Bad Request
        }

        // Condition 3: There must be enough available seats
        if ($booking->trip->available_seats < $booking->seats_number) {
            return Response::deny('There are not enough available seats.', 400); // Bad Request
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can reject the booking.
     */
    public function reject(User $user, Booking $booking)
    {
        // Condition 1: The user must be the owner of the trip
        if ($booking->trip->user_id != $user->id) {
            return Response::deny('You are not allowed to reject this booking.', 403); // Forbidden
        }

        // Condition 2: The booking must be in "pending" or "rejected" status
        if ($booking->status != "pending" && $booking->status != "rejected") {
            return Response::deny('The booking is not in a valid status for rejection.', 400); // Bad Request
        }

        return Response::allow();
    }
}
