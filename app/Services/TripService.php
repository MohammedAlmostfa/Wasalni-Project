<?php

namespace App\Services;

use Exception;
use App\Models\City;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TripService
{
    /**
     * Retrieve a paginated list of trips with filtering options.
     *
     * @param array $filteringData An array of filtering criteria (e.g., trip_start, from, to, status, etc.).
     * @return array Contains the status, message, and paginated trip data.
     */
    public function showTrips($filteringData)
    {
        try {
            $cityId = auth()->check() ? auth()->user()->profile->city_id : null;

            $trips = Trip::with([
                'user.profile' => function ($query) {
                    $query->select('user_id', 'first_name', 'last_name');
                },
                'user' => function ($query) {
                    $query->withAvg('tripRatings as avg_driver_rating', 'rate')->withCount('tripRatings as number_of_rating');
                },
                'cityFrom' => function ($query) {
                    $query->select('id', 'city_name');
                },
                'cityTo' => function ($query) {
                    $query->select('id', 'city_name');
                },
                'savedByUsers' => function ($query) {
                    $query->where('user_id', auth()->id());
                },
                 'user.roles' => function ($query) {
                     $query->select('roles.id', 'roles.name')->withPivot('image_name', 'mime_type', 'image_path');
                 }
            ])

                ->select([
                     'id AS trip_id',
                     'description',
                     'status',
                     'to',
                     'from',
                     'user_id',
                     'trip_start',
                     'seat_price',
                     'available_seats',
                     'created_at',
                 ])
                ->addSelect([
                     DB::raw('CASE WHEN EXISTS (SELECT 1 FROM trip_user WHERE trip_user.trip_id = trips.id AND trip_user.user_id = ' . auth()->id() . ') THEN 1 ELSE 0 END AS is_saved'),
                     DB::raw($cityId ? "CASE WHEN trips.from = {$cityId} THEN 0 ELSE 1 END AS city_priority" : "1 AS city_priority")
                 ])
                ->when(!empty($filteringData), function ($query) use ($filteringData) {
                    $query->filterBy($filteringData);
                })
                ->orderBy('trip_start', 'asc')
                ->orderBy('city_priority', 'asc')
                ->paginate(10);

            return [
                'message' => __('trip.show_trips_success'),
                'data' => $trips, // Grouped trips
                'status' => 200,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showTrips: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }



    /**
     * Retrieve a paginated list of trips for the authenticated user with filtering options.
     *
     * @param array $filteringData An array of filtering criteria.
     * @return array Contains the status, message, and paginated trip data.
     */
    public function showhisTrips($filteringData)
    {
        try {
            /** @var \App\Models\User $user */
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
                ->join('cities AS city_from', 'trips.from', '=', 'city_from.id')
                ->join('cities AS city_to', 'trips.to', '=', 'city_to.id')
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

    /**
     * Retrieve a paginated list of trips for a specific user with filtering options.
     *
     * @param array $filteringData An array of filtering criteria.
     * @param int $id The ID of the user whose trips are being retrieved.
     * @return array Contains the status, message, and paginated trip data.
     */
    public function showuserTrips($filteringData, $id)
    {
        try {
            $user = User::findorfail($id);
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
                ->join('cities AS city_from', 'trips.from', '=', 'city_from.id')
                ->join('cities AS city_to', 'trips.to', '=', 'city_to.id')
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

    /**
     * Create a new trip.
     *
     * @param array $data An array of trip data (e.g., description, trip_start, from, to, etc.).
     * @return array Contains the status, message, and created trip data.
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
            //forget  trips chache

            Cache::forget('trips');

            // Retrieve city names for 'from' and 'to' fields
            $fromCity = City::findorfail($data['from']);
            $toCity = City::findorfail($data['to']);
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

    /**
     * Update an existing trip.
     *
     * @param array $data An array of updated trip data.
     * @param Trip $trip The trip model to be updated.
     * @return array Contains the status, message, and updated trip data.
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
            //forget  trips chache
            Cache::forget('trips');

            // Retrieve city names for 'from' and 'to' fields
            $fromCity = City::findorfail($trip->from);
            $toCity = City::findorfail($trip->to);
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

    /**
     * Delete a trip.
     *
     * @param Trip $trip The trip model to be deleted.
     * @return array Contains the status and message.
     */
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

    /**
     * Mark a trip as "Ending".
     *
     * @param int $id The ID of the trip to be marked as ending.
     * @return array Contains the status and message.
     */
    public function endingTrip($id)
    {
        try {
            // Find the trip by ID
            $trip = Trip::findorfail($id);
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
    /**
     * Mark a trip as "on the way".
     *
     * @param int $id The ID of the trip to be marked as ending.
     * @return array Contains the status and message.
     */
    public function Onthethewaytrip($id)
    {
        try {
            // Find the trip by ID
            $trip = Trip::findorfail($id);
            // Update the trip status to "Ending"
            $trip->update([
                'status' => 'on_the_way',
            ]);

            return [
                'message' => __('trip.on_the_way_success'),
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
