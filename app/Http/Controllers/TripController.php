<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Services\TripService;
use App\Http\Resources\TripResource;
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
     * @param TripService $tripService The trip service instance.
     */
    public function __construct(TripService $tripService)
    {
        $this->tripService = $tripService;
    }

    /**
     * Display a listing of the trips.
     *
     * This method retrieves all trips using the trip service and returns a paginated response.
     *
     * @param FilteringTripsData $request The request containing filtering data.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(FilteringTripsData $request)
    {
        // Validate the request data
        $validationData = $request->validated();

        // Call the trip service to retrieve all trips
        $result = $this->tripService->showTrips($validationData);

        // Return a paginated response if the status is 200, otherwise return an error response
        return $result['status'] === 200
            ? $this->paginated($result['data'], TripResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Display the authenticated user's trips.
     *
     * This method retrieves trips associated with the authenticated user using the trip service and returns a paginated response.
     *
     * @param FilteringTripsData $request The request containing filtering data.
     * @return \Illuminate\Http\JsonResponse
     */
    public function showhisTrips(FilteringTripsData $request)
    {
        // Validate the request data
        $validationData = $request->validated();

        // Call the trip service to retrieve the authenticated user's trips
        $result = $this->tripService->showhisTrips($validationData);

        // Return a paginated response if the status is 200, otherwise return an error response
        return $result['status'] === 200
            ? $this->paginated($result['data'], TripResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Display trips for a specific user.
     *
     * This method retrieves trips associated with a specific user using the trip service and returns a paginated response.
     *
     * @param FilteringTripsData $request The request containing filtering data.
     * @param int $id The ID of the user whose trips are to be retrieved.
     * @return \Illuminate\Http\JsonResponse
     */
    public function showuserTrips(FilteringTripsData $request, $id)
    {
        // Validate the request data
        $validationData = $request->validated();

        // Call the trip service to retrieve trips for the specified user
        $result = $this->tripService->showuserTrips($validationData, $id);

        // Return a paginated response if the status is 200, otherwise return an error response
        return $result['status'] === 200
            ? $this->paginated($result['data'], TripResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }


    /**
     * Store a newly created trip in storage.
     *
     * This method validates the request data and creates a new trip using the trip service.
     *
     * @param StoreTripRequest $request The request containing trip data.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTripRequest $request)
    {
        // Authorize the user to create a trip (if needed)
        // $this->authorize('create');

        // Validate the request data
        $validationData = $request->validated();

        // Call the trip service to create the trip
        $result = $this->tripService->creattrip($validationData);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Update the specified trip in storage.
     *
     * This method validates the request data and updates the specified trip using the trip service.
     *
     * @param UpdateTripRequest $request The request containing updated trip data.
     * @param Trip $trip The trip to be updated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        // Authorize the user to update the trip
        $this->authorize('update', $trip);

        // Validate the request data
        $validationData = $request->validated();

        // Call the trip service to update the trip
        $result = $this->tripService->updateTrip($validationData, $trip);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Delete the specified trip from storage.
     *
     * This method authorizes the user to delete the trip and calls the trip service to perform the deletion.
     *
     * @param Trip $trip The trip to be deleted.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Trip $trip)
    {
        // Authorize the user to delete the trip
        $this->authorize('delete', $trip);

        // Call the trip service to delete the trip
        $result = $this->tripService->delettrip($trip);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? $this->success(null, $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * End a trip.
     *
     * This method marks a trip as ended.
     *
     * @param Trip $trip The trip to be ended.
     * @return \Illuminate\Http\JsonResponse
     */
    public function endingTrip(Trip $trip)
    {
        // Call the trip service to end the trip
        $result = $this->tripService->endingTrip($trip);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? $this->success(null, $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }
}
