<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendFcmNotificationJob;

class BookingObserver
{
    /**
     * Handle the Booking "updated" event.
     *
     * This method is triggered whenever a booking is updated.
     * It checks if the booking status has been changed to "accepted", "rejected", or "cancel",
     * and performs the corresponding actions such as adjusting the available seats of the trip.
     *
     * @param Booking $booking The booking instance that was updated.
     * @return void
     */
    public function updated(Booking $booking): void
    {
        // Check if the booking status has changed to "accepted"
        if ($booking->isDirty('status') && $booking->status === 'accepted') {
            // Reduce available seats when booking is accepted
            $this->Reducingavailableseats($booking);
        }

        // Check if the booking status has changed to "rejected"
        if ($booking->isDirty('status') && $booking->status === 'rejected') {
            // Handle booking rejection
            $this->Rejectedbooking($booking);
        }

        // Check if the booking status has changed to "cancel"
        if ($booking->isDirty('status') && $booking->status === 'cancel') {
            // Increase available seats when booking is canceled
            $this->Increaseavailableseats($booking);
        }
    }

    /**
     * Increase available seats when a booking is canceled.
     *
     * This method is triggered when a booking status changes to "cancel",
     * it increases the available seats of the trip by the number of seats the user booked.
     * A database transaction is used to ensure data consistency.
     *
     * @param Booking $booking The booking instance that was canceled.
     * @return void
     */
    protected function Increaseavailableseats(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            // Lock the trip row for update to prevent race conditions
            $trip = $booking->trip()->lockForUpdate()->first();

            // Get the user associated with the booking
            $userid = $booking->user_id;
            $user = User::findorfail($userid);

            // Send a notification to the user regarding the trip cancellation
            SendFcmNotificationJob::dispatch($user, __('notifications.booking_canceled.title'), __('notifications.booking_canceled.message'));

            // Increase the available seats of the trip by the number of seats the user booked
            $trip->available_seats += $booking->seats_number;

            // Save the updated trip information
            $trip->save();
        });
    }

    /**
     * Reduce available seats when a booking is accepted.
     *
     * This method is triggered when a booking status changes to "accepted",
     * it decreases the available seats of the trip by the number of seats the user booked.
     * A database transaction is used to ensure data consistency.
     *
     * @param Booking $booking The booking instance that was accepted.
     * @return void
     */
    protected function Reducingavailableseats(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            // Lock the trip row for update to prevent race conditions
            $trip = $booking->trip()->lockForUpdate()->first();

            // Get the user associated with the booking
            $userid = $booking->user_id;
            $user = User::findorfail($userid);

            // Send a notification to the user regarding the booking acceptance
            SendFcmNotificationJob::dispatch($user, __('notifications.booking_accepted.title'), __('notifications.booking_accepted.message'));

            // Reduce the available seats of the trip by the number of seats the user booked
            $trip->available_seats -= $booking->seats_number;

            // Save the updated trip information
            $trip->save();
        });
    }

    /**
     * Handle a booking rejection.
     *
     * This method is triggered when a booking status changes to "rejected".
     * It sends a notification to the user informing them of the rejection.
     *
     * @param Booking $booking The booking instance that was rejected.
     * @return void
     */
    public function Rejectedbooking(Booking $booking): void
    {
        // Get the user associated with the booking
        $userid = $booking->user_id;
        $user = User::findorfail($userid);

        // Send a notification to the user regarding the booking rejection
        SendFcmNotificationJob::dispatch($user, __('notifications.booking_rejected.title'), __('notifications.booking_rejected.message'));
    }
}
