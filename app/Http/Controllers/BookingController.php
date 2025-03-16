<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest\StoreBookingRequest;
use App\Http\Requests\BookingRequest\UpdateBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $BookingService;

    /**
     * Constructor to inject the BookingService.
     *
     * @param BookingService $BookingService
     */
    public function __construct(BookingService $BookingService)
    {
        $this->BookingService = $BookingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }
    public function showbookingsbytrip($id)
    {

        $result = $this->BookingService->showbookingsbytrip($id);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Store a newly created booking in storage.
     *
     * @param StoreBookingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBookingRequest $request)
    {
        // Validate the request data
        $validateddata = $request->validated();

        // Call the service to create a booking
        $result = $this->BookingService->createBooking($validateddata);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Update the specified booking in storage.
     *
     * @param UpdateBookingRequest $request
     * @param Booking $booking
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        // Authorize the update action
        $this->authorize('update', $booking);

        // Validate the request data
        $validateddata = $request->validated();

        // Call the service to update the booking
        $result = $this->BookingService->updateBooking($validateddata, $booking);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove the specified booking from storage.
     *
     * @param Booking $booking
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Booking $booking)
    {
        // Authorize the delete action
        $this->authorize('delete', $booking);

        // Call the service to delete the booking
        $result = $this->BookingService->deleteBooking($booking);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Accept a booking.
     *
     * @param Booking $booking
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(Booking $booking)
    {
        // Authorize the accept action
        $this->authorize('accept', $booking);

        // Call the service to accept the booking
        $result = $this->BookingService->acceptedBooking($booking);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Reject a booking.
     *
     * @param Booking $booking
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(Booking $booking)
    {
        // Authorize the reject action
        $this->authorize('reject', $booking);

        // Call the service to reject the booking
        $result = $this->BookingService->rejectBooking($booking);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
