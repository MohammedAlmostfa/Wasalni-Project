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
            $user = Auth::user();

            /** @var \App\Models\User $user */
            // Fetch bookings related to the authenticated user and load related trip and profile data
            $bookings = $user->bookings()
    ->with([
        // Load trip data with only required fields
        'trip' => function ($query) {
            $query->select('id', 'user_id', 'trip_start', 'from', 'to'); // Select only the relevant columns for the trip
        },

        // Load city data related to the trip's origin city (`cityFrom`)
        'trip.cityFrom' => function ($query) {
            $query->select('id', 'city_name'); // Select the `id` and `city_name` for the origin city
        },

        // Load city data related to the trip's destination city (`cityTo`)
        'trip.cityTo' => function ($query) {
            $query->select('id', 'city_name'); // Select the `id` and `city_name` for the destination city
        },

        // Load the user who created the trip
        'trip.user' => function ($query) {
            $query->select('id', 'created_at'); // Select only `id` and `created_at` for the trip owner
        },

        // Load the profile of the user who created the trip (just the first and last names)
        'trip.user.profile' => function ($query) {
            $query->select('user_id', 'first_name', 'last_name'); // Only select `user_id`, `first_name`, and `last_name`
        },

        // Load the roles of the user who made the booking, including the pivot columns
        'user.roles' => function ($query) {
            $query->select('roles.id', 'roles.name') // Select only `roles.id` and `roles.name`
                ->withPivot('image_name', 'mime_type', 'image_path'); // Include pivot data for the role
        }
    ])
    ->when(!empty($filteringData), function ($query) use ($filteringData) {
        // Apply filtering criteria if provided (e.g., status, seats_number)
        $query->filterBy($filteringData);
    })
    ->paginate(10); // Fetch data with pagination


            $sortedItems = $bookings->getCollection()->sortBy('trip.trip_start')->values();
            $bookings->setCollection($sortedItems);


            // If no bookings found, return an error response
            if (!$bookings) {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => [__('booking.booking_not_found')], // Localization for booking not found message
                    ],
                ];
            }

            // Return a success response with the bookings data
            return [
                'message' => __('booking.mybookings_retrieved'), // Localization for success message
                'data' => $bookings,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log any errors that occur
            Log::error('Error in show my booking: ' . $e->getMessage());

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

            // Fetch all bookings for the trip with user and profile details
            $bookings = $trip->bookings()->with([
                'user' => function ($query) {
                    $query->select('id'); // Load only the user ID
                },
                'user.profile' => function ($query) {
                    $query->select('user_id', 'first_name', 'last_name'); // Load profile details
                }
            ])->paginate(10); // Fetch bookings with pagination

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
    public function cancelbooking(Booking $booking)
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
