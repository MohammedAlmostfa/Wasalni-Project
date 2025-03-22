<?php

namespace App\Observers;

use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingObserver
{
    /**
     * Handle the Booking "updated" event.
     *
     * This method is triggered whenever a booking is updated.
     * It checks if the booking status was changed to "accepted" and adjusts the available seats accordingly.
     *
     * @param Booking $booking The booking that was updated.
     * @return void
     */
    public function updated(Booking $booking): void
    {
        // Check if the status was changed to "accepted"
        if ($booking->isDirty('status') && $booking->status === 'accepted') {
            $this->adjustAvailableSeats($booking);
        }
    }

    /**
     * Adjust available seats when a booking is accepted.
     *
     * This method reduces the available seats in the trip when a booking is accepted.
     * It uses a database transaction to ensure data consistency.
     *
     * @param Booking $booking The booking that was accepted.
     * @return void
     */
    protected function adjustAvailableSeats(Booking $booking)
    {
        DB::transaction(function () use ($booking) {
            // Lock the trip row for update to prevent race conditions
            $trip = $booking->trip()->lockForUpdate()->first();

            // Reduce the available seats by the number of seats booked
            $trip->available_seats -= $booking->seats_number;

            // Save the updated trip
            $trip->save();
        });
    }
}
