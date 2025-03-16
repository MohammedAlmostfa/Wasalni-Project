<?php

namespace App\Services;

use Exception;
use App\Models\City;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TripService
{
    /**
     * Retrieve all trips with optional filtering.
     *
     * This method retrieves all trips, optionally filtered by the provided data, and returns a paginated response.
     *
     * @param array $filteringData The filtering criteria.
     * @return array Returns an array containing the result of the operation.
     *               - 'message': A message describing the outcome.
     *               - 'data': The paginated list of trips.
     *               - 'status': The HTTP status code (200 for success, 500 for error).
     */
    public function showTrips($filteringData)
    {
        try {
            // Retrieve trips with necessary relationships and apply filters
            $trips = Trip::select('trips.id', 'trips.description', 'trips.status', 'trips.from', 'trips.to', 'trips.user_id', 'trips.trip_start', 'trips.seat_price', 'trips.available_seats', 'trips.created_at')
                ->join('profiles', 'trips.user_id', '=', 'profiles.user_id')
                ->join('cities AS city_from', 'trips.from', '=', 'city_from.id')
                ->join('cities AS city_to', 'trips.to', '=', 'city_to.id')
                ->addSelect('profiles.first_name', 'profiles.last_name', 'city_from.city_name AS from_city', 'city_to.city_name AS to_city')
                ->filterby($filteringData)
                ->paginate(10);

            return [
                'message' => 'All trips retrieved successfully',
                'data' => $trips,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showTrips: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while retrieving trips.'],
                ],
            ];
        }
    }

    /**
     * Retrieve trips for the authenticated user with optional filtering.
     *
     * This method retrieves trips associated with the authenticated user, optionally filtered by the provided data, and returns a paginated response.
     *
     * @param array $filteringData The filtering criteria.
     * @return array Returns an array containing the result of the operation.
     *               - 'message': A message describing the outcome.
     *               - 'data': The paginated list of trips.
     *               - 'status': The HTTP status code (200 for success, 500 for error).
     */
    public function showhisTrips($filteringData)
    {
        try {
            $user = Auth::user();
            $trips = $user->trips()
                ->select(
                    'trips.id',
                    'trips.description',
                    'trips.status',
                    'trips.created_at',
                    'trips.trip_start',
                    'trips.seat_price',
                    'trips.available_seats',
                    'city_from.city_name as from_city',
                    'city_to.city_name as to_city'
                )

                ->leftJoin('cities AS city_from', 'trips.from', '=', 'city_from.id')
                ->leftJoin('cities AS city_to', 'trips.to', '=', 'city_to.id')
                ->filterby($filteringData)
                ->paginate(10);

            return [
                'message' => 'Your trips retrieved successfully',
                'data' => $trips,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showhisTrips: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while retrieving your trips.'],
                ],
            ];
        }
    }

    /**
     * Retrieve trips for a specific user with optional filtering.
     *
     * This method retrieves trips associated with a specific user, optionally filtered by the provided data, and returns a paginated response.
     *
     * @param array $filteringData The filtering criteria.
     * @param int $id The ID of the user whose trips are to be retrieved.
     * @return array Returns an array containing the result of the operation.
     *               - 'message': A message describing the outcome.
     *               - 'data': The paginated list of trips.
     *               - 'status': The HTTP status code (200 for success, 500 for error).
     */
    public function showuserTrips($filteringData, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => ['user not found.'],
                    ],
                ];
            }

            $trips = $user->trips()
                ->select(
                    'trips.id',
                    'trips.description',
                    'trips.status',
                    'trips.created_at',
                    'trips.trip_start',
                    'trips.seat_price',
                    'trips.available_seats',
                    'city_from.city_name as from_city',
                    'city_to.city_name as to_city'
                )
                ->leftJoin('cities AS city_from', 'trips.from', '=', 'city_from.id')
                ->leftJoin('cities AS city_to', 'trips.to', '=', 'city_to.id')
                ->filterby($filteringData)
                ->paginate(10);

            return [
                'message' => 'User trips retrieved successfully',
                'data' => $trips,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showuserTrips: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while retrieving user trips.'],
                ],
            ];
        }
    }


    /**
     * Create a new trip.
     *
     * This method creates a new trip using the provided data.
     *
     * @param array $data The trip data.
     * @return array Returns an array containing the result of the operation.
     *               - 'message': A message describing the outcome.
     *               - 'data': The created trip.
     *               - 'status': The HTTP status code (200 for success, 500 for error).
     */
    public function creattrip($data)
    {
        try {
            // Create a new trip
            $trip = Trip::create([
                'description' => $data['description'],
                'trip_start' => $data['trip_start'],
                'from' => $data['from'],
                'to' => $data['to'],
                'status' => $data['status'],
                'seat_price' => $data['seat_price'],
                'available_seats' => $data['available_seats'],
                'user_id' => Auth::user()->id,
            ]);

            // Retrieve city names for 'from' and 'to' fields
            $fromCity = City::find($data['from']);
            $toCity = City::find($data['to']);
            $trip->from = $fromCity->city_name;
            $trip->to = $toCity->city_name;

            return [
                'message' => 'Trip created successfully',
                'status' => 200,
                'data' => $trip,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in createTrip: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while creating the trip.'],
                ],
            ];
        }
    }

    /**
     * Update the trip details.
     *
     * This method updates the trip details based on the provided data.
     * If a specific field is not provided in the data, the existing value of the trip is retained.
     *
     * @param array $data The data to update the trip with.
     * @param Trip $trip The trip model to be updated.
     * @return array Returns an array containing the result of the operation.
     *               - 'message': A message describing the outcome.
     *               - 'data': The updated trip data or null if an error occurred.
     *               - 'status': The HTTP status code (200 for success, 500 for error).
     */
    public function updatetrip($data, Trip $trip)
    {
        try {
            // Update the trip with the provided data
            $trip->update([
                'description' => $data['description'] ?? $trip->description,
                'trip_start' => $data['trip_start'] ?? $trip->trip_start,
                'from' => $data['from'] ?? $trip->from,
                'to' => $data['to'] ?? $trip->to,
                'status' => $data['status'] ?? $trip->status,
                'seat_price' => $data['seat_price'] ?? $trip->seat_price,
                'available_seats' => $data['available_seats'] ?? $trip->available_seats,
            ]);

            // Retrieve city names for 'from' and 'to' fields
            $fromCity = City::find($trip->from);
            $toCity = City::find($trip->to);
            $trip->from = $fromCity->city_name;
            $trip->to = $toCity->city_name;

            return [
                'message' => 'Trip updated successfully',
                'data' => $trip,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in updateTrip: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while updating the trip.'],
                ],
            ];
        }
    }

    /**
     * Delete a trip.
     *
     * This method deletes the specified trip. If the deletion is successful,
     * it returns a success response. If an exception occurs, it logs the error
     * and returns an error response.
     *
     * @param Trip $trip The trip to be deleted.
     * @return array Returns an array containing the result of the operation.
     *               - 'message': A message describing the outcome.
     *               - 'status': The HTTP status code (200 for success, 500 for error).
     */
    public function delettrip(Trip $trip)
    {
        try {
            // Delete the trip
            $trip->delete();

            return [
                'message' => 'Trip deleted successfully',
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in deleteTrip: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while deleting the trip.'],
                ],
            ];
        }
    }

    /**
     * End a trip.
     *
     * This method marks a trip as ended.
     *
     * @param Trip $trip The trip to be ended.
     * @return array Returns an array containing the result of the operation.
     *               - 'message': A message describing the outcome.
     *               - 'status': The HTTP status code (200 for success, 500 for error).
     */
    public function endingTrip(Trip $trip)
    {
        try {
            // Mark the trip as ended
            $trip->update([
                'status' => 'ended',
            ]);

            return [
                'message' => 'Trip ended successfully',
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in endingTrip: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while ending the trip.'],
                ],
            ];
        }
    }
}
