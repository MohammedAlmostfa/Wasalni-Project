<?php

namespace App\Rules;

use App\Models\Trip;
use Illuminate\Contracts\Validation\Rule;

class SeatsAvailable implements Rule
{
    protected $tripId;
    protected $availableSeats;

    public function __construct($tripId)
    {
        $this->tripId = $tripId;
    }

    public function passes($attribute, $value)
    {

        $trip = Trip::findorfail($this->tripId);
        $this->availableSeats = $trip->available_seats;

        return $value <= $this->availableSeats;
    }

    public function message()
    {

        return __('validation.seats_available', ['available_seats' => $this->availableSeats]);
    }
}
