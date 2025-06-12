<?php

namespace App\Services;

use Exception;
use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BookingService
{
    /**
     * Retrieve all bookings for the authenticated user with optional filtering.
     *
     * @param array $filteringData An associative array of filtering criteria (e.g., ['status' => 1, 'seats_number' => 2]).
     * @return array Response containing status, message, and data.
     */
    public function showMyBooking($filteringData)
    {
        try {
            $user = Auth::user();

            /** @var \App\Models\User $user */
            $bookings = $user->bookings()
                ->with([
                    'trip' => function ($query) {
                        $query->select('id', 'user_id', 'trip_start', 'from', 'to', 'available_seats', 'seat_price');
                    },
                    'trip.cityFrom:id,city_name',
                    'trip.cityTo:id,city_name',
                    'trip.user' => function ($query) {
                        $query->with([
                            'profile:id,user_id,first_name,last_name',
                            'image'
                        ])
                            ->withCount('receivedRatings as number_of_rating')
                            ->withAvg('receivedRatings as avg_driver_rating', 'rate');
                    },
                    'trip.user.image'
                ])
                ->when(!empty($filteringData), function ($query) use ($filteringData) {
                    $query->filterBy($filteringData);
                })
                ->paginate(10);

            // Sort bookings by trip start time
            $sortedItems = $bookings->getCollection()->sortBy('trip.trip_start')->values();
            $bookings->setCollection($sortedItems);

            return [
                'message' => __('booking.mybookings_retrieved'),
                'data' => $bookings,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('Error in show my booking: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')],
                ],
            ];
        }
    }


    /**
     * Retrieve all bookings for a specific trip.
     *
     * @param int $id The ID of the trip.
     * @return array Response containing status, message, and data.
     */
    public function showBookingsByTrip($id)
    {
        try {
            // Fetch all bookings for the trip with user and profile details
            $bookings = Booking::with([
                'user' => function ($query) {
                    $query->select('id'); // Load only the user ID
                },
                'user.profile' => function ($query) {
                    $query->select('user_id', 'first_name', 'last_name'); // Load profile details
                }
            ])->wherr('trip_id', $id)->paginate(10); // Fetch bookings with pagination

            // Return a success response with the bookings data
            return [
                'message' => __('booking.bookings_retrieved'), // Localization for success message
                'data' => $bookings,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log any errors that occur
            Log::error('Error in show booking by trip: ' . $e->getMessage());

            // Return a general error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')], // Localization for general error message
                ],
            ];
        }
    }

    /**
     * Create a new booking.
     *
     * @param array $data Booking data (e.g., ['trip_id' => 1, 'seats_number' => 2, 'nots' => 'Some notes']).
     * @return array Response containing status, message, and data.
     */
    public function createBooking($data)
    {
        try {
            /** @var \App\Models\User $user Authenticated user */
            $user = Auth::user(); // Get the authenticated user

            // Check if the user already has pending bookings for the same trip
            $lock = Cache::lock('trip-' . $data['trip_id'] . '-lock', 10);

            if (!$lock->get()) {
                return [
                    'status' => 429, // Too Many Requests
                    'message' => __('booking.try_again_later'),
                ];
            }
            $pendingBookings = $user->bookings()
                ->where('trip_id', $data['trip_id'])
                ->where('status', 'pending')
                ->get();

            // If pending bookings exist, return a conflict response
            if (!$pendingBookings->isEmpty()) {
                return [
                    'status' => 409,
                    'message' => [
                        'errorDetails' => [__('booking.trip_has_booking')], // Localization for conflict message
                    ],
                ];
            }

            // Retrieve the trip by ID or fail if not found
            $trip = Trip::findOrFail($data['trip_id']);

            // Create the new booking
            $booking = Booking::create([
                'trip_id' => $data['trip_id'],
                'nots' => $data['nots'] ?? null, // Optional 'nots' field
                'seats_number' => $data['seats_number'],
                'user_id' => $user->id,
            ]);

            // Return a success response with the new booking data
            return [
                'message' => __('booking.booking_created'), // Localization for success message
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log any errors that occur
            Log::error('Error in createBooking: ' . $e->getMessage());

            // Return a general error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')], // Localization for general error message
                ],
            ];
        }
    }

    /**
     * Update an existing booking.
     *
     * @param array $data Updated booking data (e.g., ['trip_id' => 1, 'seats_number' => 3, 'nots' => 'Updated notes']).
     * @param Booking $booking The booking to update.
     * @return array Response containing status, message, and data.
     */
    public function updateBooking($data, Booking $booking)
    {
        try {
            // Update the booking with new data
            $booking->update([
                'seats_number' => $data['seats_number'] ?? $booking->seats_number, // Keep current value if not provided
                'nots' => $data['nots'] ?? $booking->nots, // Keep current value if not provided
            ]);

            // Return a success response with the updated booking data
            return [
                'message' => __('booking.booking_updated'), // Localization for success message
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log any errors that occur
            Log::error('Error in updateBooking: ' . $e->getMessage());

            // Return a general error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')], // Localization for general error message
                ],
            ];
        }
    }

    /**
     * Delete a booking.
     *
     * @param Booking $booking The booking to delete.
     * @return array Response containing status and message.
     */
    public function deleteBooking(Booking $booking)
    {
        try {
            // Delete the booking
            $booking->delete();

            // Return a success response
            return [
                'message' => __('booking.booking_deleted'), // Localization for success message
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log any errors that occur
            Log::error('Error in deleteBooking: ' . $e->getMessage());

            // Return a general error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')], // Localization for general error message
                ],
            ];
        }
    }

    /**
     * Cancel a booking.
     *
     * @param Booking $booking The booking to cancel.
     * @return array Response containing status and message.
     */
    public function cancelBooking(Booking $booking)
    {
        try {
            // Check if the booking is already canceled
            if ($booking->status === 'cancel') {
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => [__('booking.booking_already_cancel')], // Localization for already canceled message
                    ],
                ];
            }

            // Update the booking status to "cancel"
            $booking->update([
                'status' => 'cancel',
            ]);

            // Return success response
            return [
                'message' => __('booking.booking_cancel'), // Localization for success message
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log any errors that occur
            Log::error('Error in cancelbooking: ' . $e->getMessage());

            // Return a general error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')], // Localization for general error message
                ],
            ];
        }
    }

    /**
     * Accept a booking.
     *
     * @param Booking $booking The booking to accept.
     * @return array Response containing status, message, and data.
     */
    public function acceptedBooking(Booking $booking)
    {
        try {
            // Check if the booking is already accepted
            if ($booking->status === 'accepted') {
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => [__('booking.booking_already_accepted')], // Localization for already accepted message
                    ],
                ];
            }

            // Update the booking status to "accepted"
            $booking->update([
                'status' => 'accepted',
            ]);

            // Return success response
            return [
                'message' => __('booking.booking_accepted'), // Localization for success message
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log any errors that occur
            Log::error('Error in acceptedBooking: ' . $e->getMessage());

            // Return a general error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')], // Localization for general error message
                ],
            ];
        }
    }

    /**
     * Reject a booking.
     *
     * @param Booking $booking The booking to reject.
     * @return array Response containing status, message, and data.
     */
    public function rejectBooking(Booking $booking)
    {
        try {
            // Check if the booking is already rejected
            if ($booking->status === 'rejected') {
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => [__('booking.booking_already_rejected')], // Localization for already rejected message
                    ],
                ];
            }

            // Update the booking status to "rejected"
            $booking->update([
                'status' => 'rejected',
            ]);

            // Return success response
            return [
                'message' => __('booking.booking_rejected'), // Localization for success message
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log any errors that occur
            Log::error('Error in rejectBooking: ' . $e->getMessage());

            // Return a general error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')], // Localization for general error message
                ],
            ];
        }
    }
}
