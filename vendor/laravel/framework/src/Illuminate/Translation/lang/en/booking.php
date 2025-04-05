<?php

/*
|--------------------------------------------------------------------------
| Authentication Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are used during authentication for various
| messages that we need to display to the user. You are free to modify
| these language lines according to your application's requirements.
|
*/

return [
    'general_error' => 'An error occurred. Please try again later.',
    'booking_created' => 'Booking created successfully.',
    'booking_updated' => 'Booking updated successfully.',
    'booking_deleted' => 'Booking deleted successfully.',
    'booking_accepted' => 'Booking accepted successfully.',
    'booking_rejected' => 'Booking rejected successfully.',
    'booking_not_found' => 'Booking not found.',
    'bookings_retrieved' => 'Bookings retrieved successfully.',
    'mybookings_retrieved' => 'Your bookings retrieved successfully.',
    'booking_already_accepted' => 'Booking is already accepted.',
    'booking_already_rejected' => 'Booking is already rejected.',
    'booking_already_cancel' => 'Booking is already canceled.',
    'update_permission_denied' => 'You are not allowed to update this booking.',
    'delete_permission_denied' => 'You are not allowed to delete this booking.',
    'accept_permission_denied' => 'You are not allowed to accept bookings.',
    'accept_not_owner' => 'You are not allowed to accept this booking.',
    'invalid_status_for_acceptance' => 'The booking is not in a valid status for acceptance.',
    'not_enough_seats' => 'Not enough seats available Currently available: :available_seats seats.',
    'reject_permission_denied' => 'You are not allowed to reject bookings.',
    'reject_not_owner' => 'You are not allowed to reject this booking.',
    'invalid_status_for_rejection' => 'The booking is not in a valid status for rejection.',
    'show_permission_denied' => 'You are not allowed to show bookings of trips.',
    'show_not_owner' => 'You are not allowed to show bookings of this trip.',
    'trip_has_booking' => 'There is a pending booking for this trip. You can modify it instead of adding a new booking.',
    "booking_cancel" => 'Booking canceled successfully',
    "cancel_permission_denied" => 'You are not allowed to cancel  booking.',
    "cancel_permission_rejected" => 'You cannot cancel the booking as it is already :status',
    "cancel_not_owner" => 'You are not allowed to cancel this booking.',
];
