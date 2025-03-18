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
    protected $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorRatingRequest $request)
    {
        $validationData = $request->validated();
        $booking = Booking::find($validationData["booking_id"]);

        $this->authorize('createRating', $booking);

        $result = $this->ratingService->createRating($validationData);

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRatingRequest $request, Rating $rating)
    {
        $this->authorize('update', $rating);

        $validationData = $request->validated();

        $result = $this->ratingService->updateRating($validationData, $rating);

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating)
    {
        $this->authorize('delete', $rating);

        $result = $this->ratingService->deleteRating($rating);

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
