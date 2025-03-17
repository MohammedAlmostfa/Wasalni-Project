<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class TripPolicy
{
    /**
     * Determine whether the user can create models.
     *
     * @param User $user The authenticated user
     * @return bool Whether the user can create a trip
     */
    public function createtrip(User $user)
    {
        // Check if the user has the 'trip.create' permission
        if (!$user->can('trip.create')) {
            // Return a custom denial message
            return Response::deny(__('trip.create_permission_denied'), 403);
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user The authenticated user
     * @param Trip $trip The trip to be updated
     * @return \Illuminate\Auth\Access\Response Whether the user can update the trip
     */
    public function updatetrip(User $user, Trip $trip)
    {
        // Check if the user has the 'trip.update' permission
        if (!$user->can('trip.update')) {
            // Return a custom denial message
            return Response::deny(__('trip.update_permission_denied'), 403);
        }

        // Deny update if the user is not the owner of the trip
        if ($trip->user_id != $user->id) {
            return Response::deny(__('trip.update_not_owner'));
        }

        // Deny update if the trip has associated bookings
        if ($trip->bookings()->exists()) {
            return Response::deny(__('trip.update_has_bookings'));
        }

        // Allow update if the user is the owner and there are no bookings
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user The authenticated user
     * @param Trip $trip The trip to be deleted
     * @return \Illuminate\Auth\Access\Response Whether the user can delete the trip
     */
    public function deletetrip(User $user, Trip $trip)
    {
        // Check if the user has the 'trip.delete' permission
        if (!$user->can('trip.delete')) {
            // Return a custom denial message
            return Response::deny(__('trip.delete_permission_denied'), 403);
        }

        // Deny deletion if the user is not the owner of the trip
        if ($trip->user_id != $user->id) {
            return Response::deny(__('trip.delete_not_owner'));
        }

        // Deny deletion if the trip has associated bookings
        if ($trip->bookings()->exists()) {
            return Response::deny(__('trip.delete_has_bookings'));
        }

        // Allow deletion if the user is the owner and there are no bookings
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user The authenticated user
     * @param Trip $trip The trip to be restored
     * @return bool Whether the user can restore the trip
     */
    public function restore(User $user, Trip $trip)
    {
        // By default, allow all users to restore trips
        return true;
    }

    /**
     * Determine whether the user can end the trip.
     *
     * @param User $user The authenticated user
     * @param Trip $trip The trip to be ended
     * @return \Illuminate\Auth\Access\Response Whether the user can end the trip
     */
    public function endedtrip(User $user, Trip $trip)
    {
        if ($trip->user_id != $user->id) {
            return Response::deny(__('trip.end_not_owner'));
        }

        if ($trip->status === "Ending") {
            return Response::deny(__('trip.end_already_ended'), 400);
        }

        return Response::allow();
    }
}
