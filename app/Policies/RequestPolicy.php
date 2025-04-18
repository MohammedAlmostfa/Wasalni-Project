<?php

namespace App\Policies;

use App\Models\Request;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RequestPolicy
{
    /**
     * Determine whether the user can create a request.
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Request $request): Response|bool
    {

        if ($user->hasRole('PrivateUser')) {
            return Response::deny(__('request.create_permission_denied'), 403);
        }


        if ($user->requests()->where('status', 'pending')->exists()) {
            return Response::deny(__('request.pending_request_exists'), 403);
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Request $request): Response|bool
    {

        if (!$user->can('request.update')) {
            return Response::deny(__('request.update_permission_denied'), 403);
        }


        if ($request->status !== 'pending') {
            return Response::deny(__('request.status_update_not_allowed'), 403);
        }

        return true;
    }
}
