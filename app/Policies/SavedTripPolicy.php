<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\Response;

class SavedTripPolicy
{
    /**
     * Determine if the user can remove a saved trip.
     *
     * This method checks if the user is authorized to remove a specific trip from their saved trips.
     * It verifies that the trip exists in the `trip_user` pivot table and that the user owns the record.
     *
     * @param User $user The authenticated user.
     * @param int $recordId The ID of the record in the `trip_user` pivot table.
     * @return Response Allow if the user is authorized; otherwise, deny with a message.
     */
    public function removeTripFromSaved(User $user, $recordId): Response
    {
        // Retrieve the record from the `trip_user` pivot table
        $record = DB::table('trip_user')->where('id', $recordId)->first();

        // Check if the record exists and belongs to the authenticated user
        if (!$record || $record->user_id !== $user->id) {
            return Response::deny(__('trip.authorization_remove'), 403); // 403 Forbidden
        }

        // Allow the user to remove the trip if all conditions are met
        return Response::allow();
    }
}
