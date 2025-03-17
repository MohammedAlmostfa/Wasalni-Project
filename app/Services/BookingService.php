<?php

namespace App\Services;

use Exception;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BookingService
{
    /**
     * Retrieve all bookings for the authenticated user.
     *
     * @return array Response containing status, message, and data
     */
    public function showbookingsbybooking()
    {
        try {
            // Retrieve the authenticated user
            $user = auth()->user();

            // Retrieve the user's bookings with pagination
            $bookings = $user->bookings()->paginate(10);
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
     * @param int $id The ID of the trip
     * @return array Response containing status, message, and data
     */
    public function showbookingsbytrip($id)
    {
        try {
            // Retrieve the trip by ID
            $trip = Trip::find($id);

            // Check if the trip exists
            if (!$trip) {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => [__('booking.trip_not_found')],
                    ],
                ];
            }

            // Retrieve the trip's bookings with related user and profile data
            $bookings = $trip->bookings()->with([
                'user' => function ($query) {
                    $query->select('id'); // Select only the user ID
                },
                'user.profile' => function ($query) {
                    $query->select('user_id', 'first_name', 'last_name');
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
     * @param array $data Booking data (trip_id, seats_number, etc.)
     * @return array Response containing status, message, and data
     */
    public function createBooking($data)
    {
        try {
            // Check if the trip exists
            $trip = Trip::find($data['trip_id']);
            if (!$trip) {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => [__('booking.trip_not_found')],
                    ],
                ];
            }

            // Create a new booking
            $booking = Booking::create([
                'trip_id' => $data['trip_id'],
                'seats_number' => $data['seats_number'],
                'user_id' => Auth::user()->id,
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
     * @param array $data Updated booking data
     * @param Booking $booking The booking to update
     * @return array Response containing status, message, and data
     */
    public function updateBooking($data, Booking $booking)
    {
        try {
            // Update the booking with new data
            $booking->fill([
                'trip_id' => $data['trip_id'] ?? $booking->trip_id,
                'seats_number' => $data['seats_number'] ?? $booking->seats_number,
            ]);

            // Save the updated booking
            $booking->save();

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
     * @param Booking $booking The booking to delete
     * @return array Response containing status and message
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
     * @param Booking $booking The booking to accept
     * @return array Response containing status, message, and data
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
     * @param Booking $booking The booking to reject
     * @return array Response containing status, message, and data
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
