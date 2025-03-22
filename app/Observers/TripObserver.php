<?php

namespace App\Observers;

use App\Models\Trip;

class TripObserver
{
    /**
     * Handle the Trip "updated" event.
     *
     * This method is triggered when a trip is updated.
     * It checks if the `available_seats` attribute was changed:
     * - If `available_seats` is now 0, it marks the trip as "Complete".
     * - If `available_seats` is greater than 0, it marks the trip as "Pending".
     *
     * @param Trip $trip The trip that was updated.
     * @return void
     */
    public function updated(Trip $trip): void
    {
        // Check if the `available_seats` attribute was changed and is now 0
        if ($trip->wasChanged('available_seats') && $trip->available_seats == 0) {
            $this->markTripAsComplete($trip);
        }

        // Check if the `available_seats` attribute was changed and is greater than 0
        if ($trip->wasChanged('available_seats') && $trip->available_seats > 0) {
            $this->markTripAsPending($trip);
        }
    }

    /**
     * Mark the trip as "Complete".
     *
     * This method updates the trip's status to "Complete" if it is not already marked as such.
     * It ensures that the status is only updated when necessary.
     *
     * @param Trip $trip The trip to mark as complete.
     * @return void
     */
    protected function markTripAsComplete(Trip $trip): void
    {
        // Only update the status if it is not already 'Complete'
        if ($trip->status !== 'Complete') {
            $trip->status = 'Complete';
            $trip->save();
        }
    }

    /**
     * Mark the trip as "Pending".
     *
     * This method updates the trip's status to "Pending" if it is not already marked as such.
     * It ensures that the status is only updated when necessary.
     *
     * @param Trip $trip The trip to mark as pending.
     * @return void
     */
    protected function markTripAsPending(Trip $trip): void
    {
        // Only update the status if it is not already 'Pending'
        if ($trip->status !== 'Pending') {
            $trip->status = 'Pending';
            $trip->save();
        }
    }
}
