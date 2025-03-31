<?php

namespace App\Observers;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewNotification;

class TripObserver
{
    /**
     * Handle the Trip "updated" event.
     *
     * This method is triggered whenever a trip is updated.
     * It checks the changes made to the `available_seats` and `status` attributes of the trip:
     * - If `available_seats` becomes 0, it marks the trip as "Complete".
     * - If `available_seats` becomes greater than 0, it marks the trip as "Pending".
     * - If the trip's status is updated to 3, it triggers a notification to users whose bookings are affected.
     *
     * @param Trip $trip The trip model that was updated.
     * @return void
     */
    public function updated(Trip $trip): void
    {
        // Check if the `available_seats` attribute has changed and is now 0
        if ($trip->wasChanged('available_seats') && $trip->available_seats == 0) {
            $this->markTripAsComplete($trip);
        }
        // Check if the `available_seats` attribute has changed and is greater than 0
        elseif ($trip->wasChanged('available_seats') && $trip->available_seats > 0) {
            $this->markTripAsPending($trip);
        }
        // Check if the `status` attribute has changed and if it is set to 3 (Assuming 'Ending' status is mapped to 3)
        elseif ($trip->wasChanged('status') && $trip->status == "Ending") {
            $this->sendNotification($trip);
        }
    }

    /**
     * Send a notification to users when the trip is ending.
     *
     * This method sends a notification to all users who have booked the trip.
     * It triggers the NewNotification with a message that the trip is ending, prompting users to make necessary arrangements.
     *
     * @param Trip $trip The trip that is ending.
     * @return void
     */
    public function sendNotification(Trip $trip)
    {
        // Ensure the trip status is 'Ending' before sending notifications
        if ($trip->status == 'Ending') {
            // Fetch all users who have booked the current trip
            $users = $trip->bookings->map(function ($booking) {
                return $booking->user; // Return the user associated with each booking
            });

            // Loop through each user and send a notification about the trip ending
            foreach ($users as $user) {
                $user->notify(new NewNotification(
                    __('notifications.trip_Completion.title'),
                    __('notifications.trip_Completion.message')
                ));

            }
        }
    }

    /**
     * Mark the trip as "Complete".
     *
     * This method updates the trip's status to "Complete" if it is not already marked as such.
     * The status is only updated if necessary to avoid redundant updates.
     *
     * @param Trip $trip The trip to mark as complete.
     * @return void
     */
    protected function markTripAsComplete(Trip $trip): void
    {
        // Only update the status if it is not already 'Complete'
        if ($trip->status !== 'Complete') {
            $trip->status = 'Complete'; // Set status to "Complete"
            $trip->save(); // Save the updated trip
            $userid=$trip->user_id;
            $user=User::findorfail($userid);
            // Send a notification to the user
            $user->notify(new NewNotification(
                __('notifications.trip_completed.title'), // Title for trip completion
                __('notifications.trip_completed.message') // Message for trip completion
            ));


        }
    }

    /**
     * Mark the trip as "Pending".
     *
     * This method updates the trip's status to "Pending" if it is not already marked as such.
     * The status is only updated if necessary to avoid redundant updates.
     *
     * @param Trip $trip The trip to mark as pending.
     * @return void
     */
    protected function markTripAsPending(Trip $trip): void
    {
        // Only update the status if it is not already 'Pending'
        if ($trip->status !== 'Pending') {
            $trip->status = 'Pending'; // Set status to "Pending"
            $trip->save(); // Save the updated trip
        }
    }
}
