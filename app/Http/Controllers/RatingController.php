<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\RatingService;
use App\Http\Requests\RatingRequest\StorRatingRequest;
use App\Http\Requests\RatingRequest\UpdateRatingRequest;

class RatingController extends Controller
{
    /**
     * The RatingService instance to handle business logic.
     *
     * @var RatingService
     */
    protected $ratingService;

    /**
     * Constructor to inject the RatingService dependency.
     *
     * @param RatingService $ratingService
     */
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * Store a newly created rating in storage.
     *
     * This method validates the request, authorizes the user, and creates a new rating.
     *
     * @param StorRatingRequest $request The validated request containing rating data.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorRatingRequest $request)
    {
        // Validate the request data
        $validationData = $request->validated();

        // Find the booking associated with the rating
        $booking = Booking::find($validationData["booking_id"]);

        // Authorize the user to create a rating for this booking
        $this->authorize('createRating', $booking);

        // Create the rating using the RatingService
        $result = $this->ratingService->createRating($validationData);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Update the specified rating in storage.
     *
     * This method validates the request, authorizes the user, and updates the rating.
     *
     * @param UpdateRatingRequest $request The validated request containing updated rating data.
     * @param Rating $rating The rating to be updated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRatingRequest $request, Rating $rating)
    {
        // Authorize the user to update the rating
        $this->authorize('update', $rating);

        // Validate the request data
        $validationData = $request->validated();

        // Update the rating using the RatingService
        $result = $this->ratingService->updateRating($validationData, $rating);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove the specified rating from storage.
     *
     * This method authorizes the user and deletes the rating.
     *
     * @param Rating $rating The rating to be deleted.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Rating $rating)
    {
        // Authorize the user to delete the rating
        $this->authorize('delete', $rating);

        // Delete the rating using the RatingService
        $result = $this->ratingService->deleteRating($rating);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
