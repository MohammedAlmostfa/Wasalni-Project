<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Request;
use Spatie\Permission\Models\Role;
use App\Jobs\SendFcmNotificationJob;

class RequestObserver
{
    /**
     * Handle the Request "updated" event.
     */
    public function updated(Request $request): void
    {
        $user = User::findOrFail($request->user_id);

        // If the request was accepted
        if ($request->status === 'accepted') {
            // Assign the "PrivateUser" role if not already assigned
            if (!$user->hasRole('PrivateUser')) {
                $user->assignRole('PrivateUser');
            }

            // Optional: Update pivot table if your role-user relation supports it
            $privateUserRole = Role::where('name', 'PrivateUser')->first();

            if ($privateUserRole) {
                $user->roles()->updateExistingPivot($privateUserRole->id, [
                    'about_user' => $request->about_user,
                    'car_type' => $request->car_type,
                ]);
            }

            // Send accepted notification
            SendFcmNotificationJob::dispatch($user, __('notifications.driver_accepted.title'), __('notifications.driver_accepted.message'));
        } else {
            // Send rejected notification
            SendFcmNotificationJob::dispatch($user, __('notifications.driver_rejected.title'), __('notifications.driver_rejected.message'));
        }
    }

    public function created(Request $request): void
    {
    }
    public function deleted(Request $request): void
    {
    }
    public function restored(Request $request): void
    {
    }
    public function forceDeleted(Request $request): void
    {
    }
}
