<?php
namespace App\Policies;

use App\Models\Trip;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can update the booking.
     */
    public function update(User $user, Booking $booking): Response
    {
        if ($booking->user_id == $user->id && $booking->status == "pending") {
            return Response::allow();
        }

        return Response::deny(__('booking.update_permission_denied'), 403); // Forbidden
    }

    /**
     * Determine whether the user can delete the booking.
     */
    public function delete(User $user, Booking $booking): Response
    {
        if ($booking->user_id == $user->id && $booking->status == "pending") {
            return Response::allow();
        }

        return Response::deny(__('booking.delete_permission_denied'), 403); // Forbidden
    }

    /**
     * Determine whether the user can restore the booking.
     */
    public function restore(User $user, Booking $booking): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can accept the booking.
     */
    public function accept(User $user, Booking $booking): Response
    {
        if (!$user->can('booking.accept')) {
            return Response::deny(__('booking.accept_permission_denied'), 403);
        }

        // Condition 1: The user must be the owner of the trip
        if ($booking->trip->user_id != $user->id) {
            return Response::deny(__('booking.accept_not_owner'), 403); // Forbidden
        }

        // Condition 2: The booking must be in "pending" or "accepted" status
        if ($booking->status != "pending" && $booking->status != "accepted") {
            return Response::deny(__('booking.invalid_status_for_acceptance'), 400); // Bad Request
        }

        // Condition 3: There must be enough available seats
        if ($booking->trip->available_seats < $booking->seats_number) {
            return Response::deny(__('booking.not_enough_seats'), 400); // Bad Request
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can reject the booking.
     */
    public function reject(User $user, Booking $booking): Response
    {
        if (!$user->can('booking.reject')) {
            return Response::deny(__('booking.reject_permission_denied'), 403);
        }

        // Condition 1: The user must be the owner of the trip
        if ($booking->trip->user_id != $user->id) {
            return Response::deny(__('booking.reject_not_owner'), 403); // Forbidden
        }

        // Condition 2: The booking must be in "pending" or "rejected" status
        if ($booking->status != "pending" && $booking->status != "rejected") {
            return Response::deny(__('booking.invalid_status_for_rejection'), 400); // Bad Request
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can show bookings for a specific trip.
     */
    public function showbookingsbytrip(User $user, $id): Response
    {
        if (!$user->can('booking.show')) {
            return Response::deny(__('booking.show_permission_denied'), 403);
        }

        $trip = Trip::find($id);
        if (!$trip) {
            return Response::deny(__('trip.not_found'), 404); // Not Found
        }

        if ($trip->user_id != $user->id) {
            return Response::deny(__('booking.show_not_owner'), 403); // Forbidden
        }

        return Response::allow();
    }
}
