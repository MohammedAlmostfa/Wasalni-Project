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

            //
            $savedTrips = $user->savedTrips()->with([
                'user.profile' => function ($query) {
                    $query->select('user_id', 'first_name', 'last_name');
                },
                'user' => function ($query) {
                    $query->withAvg('tripRatings as avg_driver_rating', 'rate')
                        ->withCount('tripRatings as number_of_rating');
                },
                'cityFrom' => function ($query) {
                    $query->select('id', 'city_name');
                },
                'cityTo' => function ($query) {
                    $query->select('id', 'city_name');
                },
                'savedByUsers' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->select('users.id');
                },
                'user.roles' => function ($query) {
                    $query->select('roles.id', 'roles.name')
                        ->withPivot('image_name', 'mime_type', 'image_path');
                }
            ])
                ->select([
                    'trips.id AS trip_id',
                    'trips.description',
                    'trips.status',
                    'trips.to',
                    'trips.from',
                    'trips.user_id',
                    'trips.trip_start',
                    'trips.seat_price',
                    'trips.available_seats',
                    'trips.created_at',
                ])
                ->addSelect([
                    DB::raw('1 AS is_saved')
                ])
                ->orderBy('trips.trip_start', 'asc')
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
