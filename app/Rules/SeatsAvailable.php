<?php

namespace App\Rules;

use App\Models\Trip;
use Illuminate\Contracts\Validation\Rule;

/**
 * Validates that the requested number of seats doesn't exceed available seats for a trip.
 *
 * Ensures trip capacity constraints are respected when booking seats.
 */
class SeatsAvailable implements Rule
{
    /**
     * The trip ID being validated against
     * @var int
     */
    protected $tripId;

    /**
     * The currently available seats count for the trip
     * @var int
     */
    protected $availableSeats;

    /**
     * Create a new rule instance.
     *
     * @param int $tripId The ID of the trip to check seat availability
     */
    public function __construct($tripId)
    {
        $this->tripId = $tripId;
    }

    /**
     * Determine if the validation passes.
     *
     * @param string $attribute The field name being validated (typically 'seats')
     * @param mixed $value The number of seats being requested
     * @return bool Returns true if sufficient seats available, false otherwise
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If trip not found
     */
    public function passes($attribute, $value)
    {
        // Retrieve the trip and store available seats count
        $trip = Trip::findOrFail($this->tripId);
        $this->availableSeats = $trip->available_seats;

        // Validate requested seats don't exceed availability
        return $value <= $this->availableSeats;
    }

    /**
     * Get the validation error message.
     *
     * @return string Translated error message with available seats count
     */
    public function message()
    {
        return __('validation.seats_available', ['available_seats' => $this->availableSeats]);
    }
}
