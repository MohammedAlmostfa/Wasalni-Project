<?php

namespace App\Observers;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendFcmNotificationJob;
use App\Notifications\NewNotification;

class TripObserver
{
    /**
     * Handle the Trip "created" event.
     *
     * This method is triggered whenever a new trip is created. It performs the following actions:
     * - Retrieves the user who created the trip.
     * - Loads the user's profile (first name and last name).
     * - Sends notifications to all users who have favorited the user who created the trip.
     *
     * @param Trip $trip The trip model that was created.
     * @return void
     */
    public function create(Trip $trip)
    {
        // Get the user who created the trip
        $userId = $trip->user_id;
        $user = User::findOrFail($userId);

        // Load profile data (first_name and last_name) for the user
        $profile = $user->profile()->select(['first_name', 'last_name'])->first();

        // Ensure profile data is available before accessing it

        $firstName = $profile->first_name;
        $lastName = $profile->last_name;


        // Combine first name and last name
        $userFullName = $firstName . ' ' . $lastName;

        // Get all users who favorited the user who created the trip
        $favoritedBy = $user->favoritedBy;

        // If there are no users who have favorited the creator, exit early
        if ($favoritedBy->isEmpty()) {
            return;
        }

        // Loop through each user who favorited the creator and send a notification
        foreach ($favoritedBy as $favoriter) {
            SendFcmNotificationJob::dispatch($favoriter, __('notifications.trip_created.title'), __('notifications.trip_created.message', [
                'user' => $userFullName,
                'from' => $trip->cityFrom->city_name,
                'to' => $trip->cityTo->city_name,
                'date' => $trip->trip_start->format('Y-m-d H:i:s'), // Formatted trip start date
            ]));
        }
    }

    /**
     * Handle the Trip "updated" event.
     *
     * This method is triggered whenever a trip is updated. It checks if specific attributes
     * such as `available_seats` or `status` have been modified and performs actions accordingly:
     * - If `available_seats` becomes 0, the trip is marked as "Complete".
     * - If `available_seats` is greater than 0, the trip is marked as "Pending".
     * - If the trip's status is updated to "Ending", a notification is sent to the affected users.
     *
     * @param Trip $trip The trip model that was updated.
     * @return void
     */
    public function updated(Trip $trip): void
    {
        // If available_seats became 0, mark the trip as Complete
        if ($trip->wasChanged('available_seats') && $trip->available_seats == 0) {
            $this->markTripAsComplete($trip);
        }
        // If available_seats became greater than 0, mark the trip as Pending
        elseif ($trip->wasChanged('available_seats') && $trip->available_seats > 0) {
            $this->markTripAsPending($trip);
        }
        // If the status changed to "Ending", send a notification to users
        elseif ($trip->wasChanged('status') && $trip->status == "Ending") {
            $this->sendNotification($trip);
        }
    }

    /**
     * Send a notification to users when the trip is ending.
     *
     * This method is triggered when the trip status is updated to "Ending". It sends a notification
     * to all users who have booked the trip, informing them that the trip is ending.
     *
     * @param Trip $trip The trip that is ending.
     * @return void
     */
    public function sendNotification(Trip $trip)
    {
        // Ensure the trip status is 'Ending' before sending notifications
        if ($trip->status == 'Ending') {
            // Fetch all users who have booked the current trip
            $users = $trip->bookings->where('status', 'accepted')  ->map(function ($booking) {
                return $booking->user;
            });
            $cityfrom =$trip->cityFrom->city_name;
            $cityto =$trip->cityTo->city_name;


            // Loop through each user and send a notification about the trip ending
            foreach ($users as $user) {
                SendFcmNotificationJob::dispatch($user, $cityto, $cityfrom, __('notifications.trip_Ending.title'), 'trip_Ending');

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
            $cityfrom =$trip->cityFrom->city_name;
            $cityto =$trip->cityTo->city_name;

            // Get the user who created the trip
            $userid = $trip->user_id;
            $user = User::findOrFail($userid);

            // Send a notification to the user
            SendFcmNotificationJob::dispatch($user, $cityto, $cityfrom, __('notifications.trip_booking_completed.title'), 'trip_booking_completed');

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
