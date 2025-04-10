<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SavedTripService;
use App\Http\Resources\SavedTripResource;
use App\Http\Resources\GroupedTripsResource;
use App\Http\Requests\TripRequest\FilteringTripsData;
use App\Http\Requests\SavedTripRequst\AddToSavedTripRequest;
use App\Http\Requests\SavedTripRequst\DeletfromSavedTripRequest;

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
        $tripId = $validatedData['tripId'];

        // Check authorization using policy
        $this->authorize('removeFromSavedTrip', $tripId);

        // Call the service to remove the trip from saved trips
        $result = $this->savedTripService->removeFromSavedTrip($validatedData);

        // Return success or error response based on the service result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
