<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

/**
 * Validation rule to verify if a trip is already saved by the authenticated user.
 *
 * Ensures the trip exists in the user's saved trips before allowing operations.
 */
class CheckTripSaved implements Rule
{
    /**
     * The trip ID being validated
     * @var mixed
     */
    protected $tripId;

    /**
     * Create a new rule instance.
     *
     * @param mixed $tripId The trip ID to validate against user's saved trips
     */
    public function __construct($tripId)
    {
        $this->tripId = $tripId;
    }

    /**
     * Determine if the validation passes.
     *
     * @param string $attribute The field name being validated
     * @param mixed $value The trip ID value to check
     * @return bool Returns true if the trip is saved by user, false otherwise
     */
    public function passes($attribute, $value)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verify the trip exists in user's saved trips
        return $user->savedTrips()->where('trip_id', $value)->exists();
    }

    /**
     * Get the validation error message when check fails.
     *
     * @return string The translated error message
     */
    public function message()
    {
        return __('trip.trip_unsaved_before');
    }
}
