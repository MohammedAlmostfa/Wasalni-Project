<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\Response;

class SavedTripPolicy
{
    /**
     * Determine if the user can remove a saved trip.
     *
     * @param User $user
     * @param array $data
     * @return Response
     */

    public function removeTripFromSaved(User $user, $recordId): Response
    {
        $record = DB::table('trip_user')->where('id', $recordId)->first();

        if (!$record || $record->user_id !== $user->id) {
            return Response::deny(__('trip.authorization_remove'), 403);
        }

        return Response::allow();
    }

}
