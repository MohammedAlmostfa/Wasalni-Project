<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RateRequest\StoreRateData;
use App\Http\Requests\RateRequest\UpdateRateData;
use App\Http\Requests\RatingRequest\StoreRatingData;
use App\Http\Requests\RatingRequest\UpdateRatingData;
use App\Models\Rating;
use App\Services\RatingService;
use Illuminate\Http\JsonResponse;


/**
 * Class RatingController
 * 
 * Manages rating-related HTTP requests, including retrieval, creation, updating, and deletion.
 */
class RatingController extends Controller
{
    /**
     * RatingService instance to handle business logic.
     *
     * @var RatingService
     */
    private $ratingService;

    /**
     * Constructor: Inject the RatingService dependency.
     *
     * @param RatingService $ratingService Service layer for rating functionality.
     */
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }


    /**
     * Store a new rating in the database.
     *
     * @param StoreRateData $request Validated request data for storing a rating.
     * @return JsonResponse Response indicating success or failure.
     */
    public function store(StoreRatingData $request)
    {
        // Validate incoming request data
        $validatedData = $request->validated();

        // Process storing rating via service layer
        $result = $this->ratingService->storeRate($validatedData);

        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Update an existing rating record.
     *
     * @param UpdateRateData $request Validated request data for updating a rating.
     * @param int $id The ID of the rating to be updated.
     * @return JsonResponse Response indicating success or failure.
     */
    public function update(UpdateRatingData $request, $id)
    {
        // Validate incoming request data
        $validatedData = $request->validated();

        // Retrieve the existing rating record
        $rating = Rating::findOrFail($id);

        // Update the rating via service layer
        $result = $this->ratingService->updateRate($rating, $validatedData);

        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Soft delete a rating record.
     *
     * @param Rating $rating The rating model instance to be deleted.
     * @return JsonResponse Response indicating success or failure.
     */
    public function destroy(Rating $rating)
    {
        // Perform soft deletion via service layer
        $result = $this->ratingService->deleteRate($rating);

        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}