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
        if (!$request->isDirty('status')) {
            return;
        }

        $user = User::findOrFail($request->user_id);

        if ($request->status === 'accepted') {
            if (!$user->hasRole('PrivateUser')) {
                $user->assignRole('PrivateUser');
            }

            $privateUserRole = Role::where('name', 'PrivateUser')->first();
            if ($privateUserRole) {
                $user->roles()->updateExistingPivot($privateUserRole->id, [
                    'about_user' => $request->about_user,
                    'car_type' => $request->car_type,
                ]);
            }

            // Send notification
            SendFcmNotificationJob::dispatch($user, null, null, __('notifications.driver_accepted.title'), 'driver_accepted');

        } elseif($request->status === 'rejected') {
            if ($user->image) {
                $user->image()->delete();
            }

            if ($user->carImage) {
                $user->carImage()->delete();
            }

            SendFcmNotificationJob::dispatch($user, null, null, __('notifications.driver_rejected.title'), 'driver_rejected');


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
