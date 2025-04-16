<?php

return [

    // =======================
    // Booking notifications
    // =======================
    'booking_accepted' => [
        'title' => 'Booking Confirmed',
        'message' => 'Your booking has been confirmed. We’re excited to have you with us!',
    ],

    'booking_canceled' => [
        'title' => 'Booking Canceled',
        'message' => 'Your booking has been canceled. We apologize for any inconvenience caused.',
    ],

    'booking_rejected' => [
        'title' => 'Booking Rejected',
        'message' => 'Unfortunately, your booking has been rejected. We’re sorry for the inconvenience.',
    ],


    // =======================
    // Trip-related notifications
    // =======================
    'trip_completed' => [
        'title' => 'Trip Completed',
        'message' => 'Your trip has ended. Please take a moment to rate your experience—your feedback helps us improve.',
    ],

    'trip_booking_completed' => [
        'title' => 'Trip Booking Completed',
        'message' => 'Your trip booking has been successfully completed.',
    ],

    'trip_created' => [
        'title' => 'New Trip Added',
        'message' => ':user just added a new trip from :from to :to on :date. Don’t miss it!',
    ],


    // =======================
    // Driver (PrivateUser) status notifications
    // =======================
    'driver_accepted' => [
        'title' => 'Driver Request Approved',
        'message' => 'Great news! Your request to become a driver has been approved. Welcome aboard!',
    ],

    'driver_rejected' => [
        'title' => 'Driver Request Declined',
        'message' => 'We’re sorry. Your request to become a driver was declined. Feel free to try again in the future.',
    ],

];
