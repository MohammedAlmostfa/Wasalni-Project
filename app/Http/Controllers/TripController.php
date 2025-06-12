<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Services\TripService;
use App\Http\Resources\UserTrips;
use App\Http\Resources\TripResource;
use App\Http\Resources\GroupedTripsResource;
use App\Http\Requests\TripRequest\StoreTripRequest;
use App\Http\Requests\TripRequest\UpdateTripRequest;
use App\Http\Requests\TripRequest\FilteringTripsData;

class TripController extends Controller
{
    /**
     * The trip service instance.
     *
     * @var TripService
     */
    protected $tripService;

    /**
     * Create a new TripController instance.
     *
     * This constructor injects the trip service which will be used to handle business logic for trips.
     *
     * @param TripService $tripService The trip service instance.
     */
    public function __construct(TripService $tripService)
    {
        $this->tripService = $tripService;  // Initialize the trip service to be used throughout the controller
    }

    /**
     * Display a listing of the trips.
     *
     * This method retrieves trips from the service and returns them with pagination, applying any filters from the request.
     *
     * @param FilteringTripsData $request The request containing filtering data for the trips.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(FilteringTripsData $request)
    {
        $validationData = $request->validated();

        $result = $this->tripService->showTrips($validationData);

        return $result['status'] === 200
            ? response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'data' => new GroupedTripsResource($result['data']->getCollection()),
                'pagination' => [
                    'total' => $result['data']->total(),
                    'count' => $result['data']->count(),
                    'per_page' => $result['data']->perPage(),
                    'current_page' => $result['data']->currentPage(),
                    'total_pages' => $result['data']->lastPage(),
                ],
            ], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }


    /**
     * Display the authenticated user's trips.
     *
     * This method retrieves the trips that belong to the currently authenticated user and returns a paginated response.
     *
     * @param FilteringTripsData $request The request containing filtering data for the user's trips.
     * @return \Illuminate\Http\JsonResponse
     */
    public function showhisTrips(FilteringTripsData $request)
    {
        // Validate the request data
        $validationData = $request->validated();

        // Retrieve trips for the authenticated user via the trip service
        $result = $this->tripService->showHisTrips($validationData);

        // Return paginated response if status is 200, or return an error message
        return $result['status'] === 200
            ? $this->paginated($result['data'], UserTrips::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Display trips for a specific user.
     *
     * This method allows you to retrieve trips associated with a specific user, based on the user ID.
     *
     * @param FilteringTripsData $request The request containing filtering data for the user's trips.
     * @param int $id The ID of the user whose trips should be retrieved.
     * @return \Illuminate\Http\JsonResponse
     */
    public function showuserTrips(FilteringTripsData $request, $id)
    {
        // Validate the request data
        $validationData = $request->validated();

        // Call the service to retrieve trips for the specified user
        $result = $this->tripService->showUserTrips($validationData, $id);

        // Return paginated response if the status is 200; otherwise, return an error message
        return $result['status'] === 200
            ? $this->paginated($result['data'], UserTrips::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Store a newly created trip in storage.
     *
     * This method validates the incoming request data and creates a new trip using the trip service.
     *
     * @param StoreTripRequest $request The request containing the trip data to be stored.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTripRequest $request)
    {
        // Authorize the user to create a new trip
       $this->authorize('createtrip', Trip::class);

        // Validate the incoming data from the request
        $validationData = $request->validated();

        // Call the trip service to create the trip
        $result = $this->tripService->creatTrip($validationData);

        // Return success or error response based on the result from the trip service
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Update the specified trip in storage.
     *
     * This method validates the updated trip data and uses the trip service to update the trip.
     *
     * @param UpdateTripRequest $request The request containing the updated trip data.
     * @param Trip $trip The trip that will be updated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        // Authorize the user to update this specific trip
        $this->authorize('updatetrip', $trip);

        // Validate the incoming request data
        $validationData = $request->validated();

        // Call the trip service to update the trip with the validated data
        $result = $this->tripService->updateTrip($validationData, $trip);

        // Return a success or error response based on the result of the update
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Delete the specified trip from storage.
     *
     * This method authorizes the user to delete the trip and calls the service to perform the deletion.
     *
     * @param Trip $trip The trip to be deleted.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Trip $trip)
    {
        // Authorize the user to delete this specific trip
        $this->authorize('deletetrip', $trip);

        // Call the trip service to delete the trip
        $result = $this->tripService->deleteTrip($trip);

        // Return a success or error response based on the deletion result
        return $result['status'] === 200
            ? $this->success(null, $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * End a trip.
     *
     * This method marks the trip as ended, authorizing the user before making the change.
     *
     * @param int $id The ID of the trip to be ended.
     * @return \Illuminate\Http\JsonResponse
     */
    public function endingTrip($id)
    {
        // Find the trip by ID, or fail if not found
        $trip = Trip::findOrFail($id);

        // Authorize the user to end this trip
        $this->authorize('endedtrip', $trip);

        // Call the trip service to mark the trip as ended
        $result = $this->tripService->endingTrip($id);

        // Return a success or error response based on the result of ending the trip
        return $result['status'] === 200
            ? $this->success(null, $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }
}
