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
     * Show all bookings.
     *
     * @return array Response containing status, message, and data
     */
    public function showbookingsbytrip($id)
    {
        try {
            $trip = Trip::with(['bookings.user.profile'])->find($id);


            if (!$trip) {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => ['Trip not found.'],
                    ],
                ];
            }


            return [
                'message' => 'All bookings retrieved successfully',
                'data' => $trip,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showhisbookings: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['Failed to retrieve bookings.'],
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
                        'errorDetails' => ['Trip not found.'],
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
                'message' => 'Booking created successfully',
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in createBooking: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['Failed to create booking.'],
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
            // Fill the booking with new data
            $booking->fill([
                'trip_id' => $data['trip_id'] ?? $booking->trip_id,
                'seats_number' => $data['seats_number'] ?? $booking->seats_number,
            ]);

            // Save the updated booking
            $booking->save();

            // Return success response
            return [
                'message' => 'Booking updated successfully',
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in updateBooking: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['Failed to update booking.'],
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
                'message' => 'Booking deleted successfully',
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in deleteBooking: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['Failed to delete booking.'],
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
                        'errorDetails' => ['Booking is already accepted.'],
                    ],
                ];
            }

            // Update the booking status to "accepted"
            $booking->update([
                'status' => 'accepted',
            ]);

            // Return success response
            return [
                'message' => 'Booking accepted successfully',
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in acceptedBooking: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['Failed to accept booking.'],
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
                        'errorDetails' => ['Booking is already rejected.'],
                    ],
                ];
            }

            // Update the booking status to "rejected"
            $booking->update([
                'status' => 'rejected',
            ]);

            // Return success response
            return [
                'message' => 'Booking rejected successfully',
                'status' => 200,
                'data' => $booking,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in rejectBooking: ' . $e->getMessage());

            // Return an error message and status
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['Failed to reject booking.'],
                ],
            ];
        }
    }
}
