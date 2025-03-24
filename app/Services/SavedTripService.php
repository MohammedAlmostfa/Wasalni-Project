<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Service class for managing user saved trips operations.
 */
class SavedTripService
{
    /**
     * Retrieve a paginated list of saved trips with filtering options.
     *
     * @param array|null $filteringData An array of filtering criteria (optional).
     * @return array Contains the status, message, and paginated trip data.
     */
    public function showsavedtrip($filteringData)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Fetch the user's saved trips
            $savedTrips = $user->savedTrips()
            ->select(
                'trips.id As trip_id',
                'trips.description',
                'trips.status',
                'trips.user_id',
                'trips.trip_start',
                'trips.seat_price',
                'trips.available_seats',
                'trips.created_at',
            )
            ->join('profiles', 'trips.user_id', '=', 'profiles.user_id')
            ->join('cities AS city_from', 'trips.from', '=', 'city_from.id')
            ->join('cities AS city_to', 'trips.to', '=', 'city_to.id')
            ->addSelect(
                'profiles.first_name',
                'profiles.last_name',
                'city_from.city_name AS from_city',
                'city_to.city_name AS to_city',
                'trip_user.created_at AS saved_at',
            )
            ->when($filteringData, function ($query, $filteringData) {
                // Apply filtering logic here
                return $query->filterBy($filteringData);
            })
            ->orderBy('saved_at', 'asc')
            ->paginate(10);

            // Return success response with saved trips data
            return [
                'status' => 200,
                'message' => 'Success',
                'data' => $savedTrips,
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in showsavedtrip: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }

    /**
     * Add a trip to the user's saved trips list.
     *
     * @param array $data Contains the trip ID to be saved.
     * @return array Contains the status and operation message.
     */
    public function addToSavedTrip($data)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $tripId = $data["tripId"];
            // Attach the trip (with timestamp)
            $user->savedTrips()->attach($tripId, ['created_at' => Carbon::now()]);

            return [
                'status' => 200,
                'message' => __("trip.trip_saved_successfully"),
            ];

        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in addToSavedTrip: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }

    /**
     * Remove a trip from the user's saved trips list.
     *
     * @param array $data Contains the trip ID to be removed.
     * @return array Contains the status and operation message.
     */
    public function removeFromSavedTrip($data)
    {
        try {
            /** @var \App\Models\User $user */

            // Delete the record from the trip_user table
            $user = Auth::user();
            $tripId = $data["tripId"];
            // Detach the trip from the user's saved trips
            $user->savedTrips()->detach($tripId);

            // Return success response
            return [
                'status' => 200,
                'message' =>  __("trip.trip_removed_successfully")
            ];
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in removeFromSavedTrip: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }
}
