<?php

namespace App\Observers;

use App\Models\Trip;

class TripObserver
{


    /**
     * Handle the Trip "updated" event.
     *
     * This method is triggered when a trip is updated.
     * It checks if the `available_seats` attribute was changed and is now 0.
     * If so, it marks the trip as complete.
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
    }


    /**
     * Mark the trip as complete.
     *
     * This method updates the trip's status to "Complete" if it is not already marked as such.
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
}
