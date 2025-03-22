<?php

namespace App\Services;

use Exception;
use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BookingService
{
    /**
     * Retrieve all bookings for the authenticated user with optional filtering.
     *
     * @param array $filteringData An associative array of filtering criteria (e.g., ['status' => 1, 'seats_number' => 2]).
     * @return array Response containing status, message, and data.
     */
    public function showmybooking($filteringData)
    {
        try {
            // Retrieve the authenticated user
            $user = auth()->user();

            // Retrieve the user's bookings with pagination and optional filtering
            $bookings = Booking::join('trips', 'bookings.trip_id', '=', 'trips.id')
                ->join('cities as cityFrom', 'trips.from', '=', 'cityFrom.id')
                ->join('cities as cityTo', 'trips.to', '=', 'cityTo.id')
                ->join('users', 'trips.user_id', '=', 'users.id')
                ->join('profiles', 'users.id', '=', 'profiles.user_id')
                ->select(
                    'bookings.id as id',
                    'bookings.seats_number',
                    'bookings.nots',
                    'bookings.status',
                    'trips.trip_start',
                    'trips.seat_price',
                    'trips.user_id as driver_id',
                    'cityFrom.city_name as from_city',
                    'cityTo.city_name as to_city',
                    'profiles.first_name',
                    'profiles.last_name'
                )
                ->where('bookings.user_id', $user->id)
                ->orderBy('trips.trip_start', 'asc')
                ->filterby($filteringData) // Apply filtering if provided
                ->paginate(10);

            // If no bookings are found, return a 404 error
            if (!$bookings) {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => [__('booking.booking_not_found')],
                    ],
                ];
            }

            // Return success response with bookings data
            return [
                'message' => __('booking.mybookings_retrieved'),
                'data' => $bookings,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in show my booking: ' . $e->getMessage());

            // Return error response
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
    public function showbookingsbytrip($id)
    {
        try {
            // Retrieve the trip by ID or fail if not found
            $trip = Trip::findOrFail($id);

            // Retrieve the trip's bookings with related user and profile data
            $bookings = $trip->bookings()->with([
                'user' => function ($query) {
                    $query->select('id'); // Select only the user ID
                },
                'user.profile' => function ($query) {
                    $query->select('user_id', 'first_name', 'last_name'); // Select profile details
                }
            ])->paginate(10);

            // Return success response with bookings data
            return [
                'message' => __('booking.bookings_retrieved'),
                'data' => $bookings,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in show booking by trip: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')],
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
            $user = auth()->user(); // Get the authenticated user

            // Check if the user has any pending bookings for the same trip
            $pendingBookings = $user->bookings()
                ->where('trip_id', $data['trip_id'])
                ->where('status', 'pending')
                ->get();

            // If pending bookings exist, return a conflict response
            if (!$pendingBookings->isEmpty()) {
                return [
                    'status' => 409,
                    'message' => [
                        'errorDetails' => [__('booking.trip_has_booking')],
                    ],
                ];
            }

            // Retrieve the trip by ID or fail if not found
            $trip = Trip::findOrFail($data['trip_id']);

            // Create a new booking
            $booking = Booking::create([
                'trip_id' => $data['trip_id'],
                'nots' => $data['nots'] ?? null,
                'seats_number' => $data['seats_number'],
                'user_id' => $user->id,
            ]);

            // Return success response
            return [
                'message' => __('booking.booking_created'),
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in createBooking: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')],
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
                'seats_number' => $data['seats_number'] ?? $booking->seats_number,
                'nots' => $data['nots'] ?? $booking->nots,
            ]);

            // Return success response
            return [
                'message' => __('booking.booking_updated'),
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in updateBooking: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')],
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

            // Return success response
            return [
                'message' => __('booking.booking_deleted'),
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in deleteBooking: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')],
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
                        'errorDetails' => [__('booking.booking_already_accepted')],
                    ],
                ];
            }

            // Update the booking status to "accepted"
            $booking->update([
                'status' => 'accepted',
            ]);

            // Return success response
            return [
                'message' => __('booking.booking_accepted'),
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in acceptedBooking: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')],
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
                        'errorDetails' => [__('booking.booking_already_rejected')],
                    ],
                ];
            }

            // Update the booking status to "rejected"
            $booking->update([
                'status' => 'rejected',
            ]);

            // Return success response
            return [
                'message' => __('booking.booking_rejected'),
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in rejectBooking: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('booking.general_error')],
                ],
            ];
        }
    }
}
