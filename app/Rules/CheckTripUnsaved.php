<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

/**
 * Validation rule to check if a trip is not already saved by the authenticated user.
 *
 * Prevents duplicate entries in user's saved trips.
 */
class CheckTripUnsaved implements Rule
{
    /**
     * The trip ID being validated
     * @var mixed
     */
    protected $tripId;

    /**
     * Create a new rule instance.
     *
     * @param mixed $tripId The trip ID to validate
     */
    public function __construct($tripId)
    {
        $this->tripId = $tripId;
    }

    /**
     * Determine if the validation passes.
     *
     * @param string $attribute The attribute being validated
     * @param mixed $value The value of the attribute
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if the trip is not already saved by the user
        return !$user->savedTrips()->where('trip_id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('trip.trip_saved_before');
    }
}
