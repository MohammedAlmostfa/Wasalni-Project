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
     *
     * @param User $user The authenticated user.
     * @param Booking $booking The booking to update.
     * @return Response Allow if the user owns the booking and it's in "pending" status; otherwise, deny.
     */
    public function update(User $user, Booking $booking): Response
    {
        // Allow if the user owns the booking and it's in "pending" status
        if ($booking->user_id == $user->id && $booking->status == "pending") {
            return Response::allow();
        }

        // Deny with a 403 Forbidden response
        return Response::deny(__('booking.update_permission_denied'), 403);
    }

    /**
     * Determine whether the user can delete the booking.
     *
     * @param User $user The authenticated user.
     * @param Booking $booking The booking to delete.
     * @return Response Allow if the user owns the booking and it's in "pending" status; otherwise, deny.
     */
    public function delete(User $user, Booking $booking): Response
    {
        // Allow if the user owns the booking and it's in "pending" status
        if ($booking->user_id == $user->id && $booking->status == "pending") {
            return Response::allow();
        }

        // Deny with a 403 Forbidden response
        return Response::deny(__('booking.delete_permission_denied'), 403);
    }

    /**
     * Determine whether the user can cancel the booking.
     *
     * @param User $user The authenticated user.
     * @param Booking $booking The booking to cancel.
     * @return Response Allow if the user has permission and the booking is in a valid status; otherwise, deny.
     */
    public function cancel(User $user, Booking $booking): Response
    {
        // Check if the user has permission to cancel bookings
        if (!$user->hasPermissionTo('booking.cancel')) {
            return Response::deny(__('booking.cancel_permission_denied'), 403);
        }

        // Allow if the user owns the trip and the booking is "accepted"
        if ($booking->trip->user_id == $user->id && $booking->status === 'accepted') {
            return Response::allow();
        }

        // Deny if the booking is already "rejected" or "pending"
        if ($booking->status == "rejected" || $booking->status == "pending") {
            return Response::deny(__('booking.cancel_permission_rejected', ['status' => $booking->status]), 403);
        }

        // Deny if the user does not own the trip
        if ($booking->trip->user_id != $user->id) {
            return Response::deny(__('booking.cancel_not_owner'), 403);
        }

        // Allow if none of the above conditions are met
        return Response::allow();
    }

    /**
     * Determine whether the user can restore the booking.
     *
     * @param User $user The authenticated user.
     * @param Booking $booking The booking to restore.
     * @return Response Always allow (no restrictions).
     */
    public function restore(User $user, Booking $booking): Response
    {
        // Always allow restoring a booking
        return Response::allow();
    }

    /**
     * Determine whether the user can accept the booking.
     *
     * @param User $user The authenticated user.
     * @param Booking $booking The booking to accept.
     * @return Response Allow if the user has permission, owns the trip, and the booking is in a valid status with enough seats; otherwise, deny.
     */
    public function accept(User $user, Booking $booking): Response
    {
        // Check if the user has permission to accept bookings
        if (!$user->hasPermissionTo('booking.accept')) {
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
        $available_seats = $booking->trip->available_seats;
        if ($available_seats < $booking->seats_number) {
            return Response::deny(__('booking.not_enough_seats', ['available_seats' => $available_seats]), 400); // Bad Request
        }

        // Allow if all conditions are met
        return Response::allow();
    }

    /**
     * Determine whether the user can reject the booking.
     *
     * @param User $user The authenticated user.
     * @param Booking $booking The booking to reject.
     * @return Response Allow if the user has permission, owns the trip, and the booking is in a valid status; otherwise, deny.
     */
    public function reject(User $user, Booking $booking): Response
    {
        // Check if the user has permission to reject bookings
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

        // Allow if all conditions are met
        return Response::allow();
    }

    /**
     * Determine whether the user can show bookings for a specific trip.
     *
     * @param User $user The authenticated user.
     * @param int $id The ID of the trip.
     * @return Response Allow if the user has permission and owns the trip; otherwise, deny.
     */
    public function showbookingsbytrip(User $user, $id): Response
    {
        // Check if the user has permission to show bookings
        if (!$user->hasPermissionTo('booking.show')) {
            return Response::deny(__('booking.show_permission_denied'), 403);
        }

        // Find the trip by ID
        $trip = Trip::find($id);
        if (!$trip) {
            return Response::deny(__('trip.not_found'), 404); // Not Found
        }

        // Condition: The user must be the owner of the trip
        if ($trip->user_id != $user->id) {
            return Response::deny(__('booking.show_not_owner'), 403); // Forbidden
        }

        // Allow if all conditions are met
        return Response::allow();
    }
}
