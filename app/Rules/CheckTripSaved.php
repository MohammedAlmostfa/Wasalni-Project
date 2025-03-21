<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckTripSaved implements Rule
{
    protected $tripId;

    public function __construct($tripId)
    {
        $this->tripId = $tripId;
    }

    public function passes($attribute, $value)
    {
        $user = Auth::user();

        return !$user->savedTrips()->where('trip_id', $value)->exists();
    }

    public function message()
    {
        return __('trip.trip_saved_before');
    }
}
