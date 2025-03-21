<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SavedTripService;
use App\Http\Requests\TripRequest\FilteringTripsData;
use App\Http\Requests\SavedTripRequst\AddToSavedTripRequest;
use App\Http\Requests\SavedTripRequst\DeletfromSavedTripRequest;
use App\Http\Resources\SavedTripResource;

class SavedTripController extends Controller
{
    protected $savedTripService;

    /**
     * Constructor.
     *
     * @param SavedTripService $savedTripService
     */
    public function __construct(SavedTripService $savedTripService)
    {
        $this->savedTripService = $savedTripService;
    }

    /**
     * Display all saved trips for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(FilteringTripsData $request)
    {
        // Validate the incoming request data
        $validateddata = $request->validated();

        // Call the service to fetch saved trips
        $result = $this->savedTripService->showsavedtrip($validateddata);

        // Return paginated response if status is 200, or return an error message
        return $result['status'] === 200
            ? $this->paginated($result['data'], SavedTripResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Add a trip to the user's saved trips list.
     *
     * @param AddToSavedTripRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function AddToSavedTrip(AddToSavedTripRequest $request)
    {
        // Validate the incoming request data
        $validateddata = $request->validated();

        // Call the service to add the trip to saved trips
        $result = $this->savedTripService->addToSavedTrip($validateddata);

        // Return success or error response based on the service result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove a trip from the user's saved trips list.
     *
     * @param DeletfromSavedTripRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromSavedTrip(DeletfromSavedTripRequest $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validated();

        // Extract the record ID from the validated data
        $recordId = $validatedData['recordId'];

        // Check authorization using policy
        $this->authorize('removeFromSavedTrip', $recordId);

        // Call the service to remove the trip from saved trips
        $result = $this->savedTripService->removeFromSavedTrip($validatedData);

        // Return success or error response based on the service result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
