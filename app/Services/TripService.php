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
                'message' => __('trip.show_trips_success'),
                'data' => $trips,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showTrips: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('trip.show_trips_error')],
                ],
            ];
        }
    }

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
                'message' => __('trip.show_your_trips_success'),
                'data' => $trips,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showhisTrips: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                   'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }

    public function showuserTrips($filteringData, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => [__('auth.not_found')],
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
                'message' => __('trip.show_user_trips_success'),
                'data' => $trips,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showuserTrips: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                      'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }

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
                'message' => __('trip.create_success'),
                'status' => 200,
                'data' => $trip,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in createTrip: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                      'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }

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
                'message' => __('trip.update_success'),
                'data' => $trip,
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in updateTrip: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                     'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }

    public function delettrip(Trip $trip)
    {
        try {
            // Delete the trip
            $trip->delete();

            return [
                'message' => __('trip.delete_success'),
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in deleteTrip: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                      'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }

    public function endingTrip($id)
    {
        try {
            // Find the trip by ID
            $trip = Trip::find($id);

            // Check if the trip exists
            if (!$trip) {
                return [
                    'message' => __('trip.not_found'),
                    'status' => 404,
                ];
            }
            // Update the trip status to "Ending"
            $trip->update([
                'status' => 'Ending',
            ]);

            return [
                'message' => __('trip.end_success'),
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the exception for debugging
            Log::error('Error in endingTrip: ' . $e->getMessage());

            // Return a generic error response
            return [
                'status' => 500,
                'message' => [
                  'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }
}
