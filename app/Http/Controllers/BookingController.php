<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Http\Resources\BookingResource;
use App\Http\Resources\MyBookingResource;
use App\Http\Requests\Bookingrequest\FilterinBookingData;
use App\Http\Requests\BookingRequest\StoreBookingRequest;
use App\Http\Requests\BookingRequest\UpdateBookingRequest;

class BookingController extends Controller
{
    /**
     * The BookingService instance used to handle booking-related logic.
     *
     * @var BookingService
     */
    protected $BookingService;

    /**
     * Constructor to inject the BookingService.
     *
     * @param BookingService $BookingService The service used for booking operations.
     */
    public function __construct(BookingService $BookingService)
    {
        $this->BookingService = $BookingService;
    }

    /**
     * Display a list of all bookings for the authenticated user.
     *
     * @param FilterinBookingData $request The request containing filtering data.
     * @return \Illuminate\Http\JsonResponse Paginated list of bookings.
     */
    public function index(FilterinBookingData $request)
    {
        // Validate the incoming request data
        $validateddata = $request->validated();

        // Retrieve bookings for the current authenticated user
        $result = $this->BookingService->showmybooking($validateddata);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? $this->paginated($result['data'], MyBookingResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Display a list of bookings for a specific trip.
     *
     * @param int $id The ID of the trip.
     * @return \Illuminate\Http\JsonResponse Paginated list of bookings for the trip.
     */
    public function showbookingsbytrip($id)
    {
        // Authorize the action to view bookings for the trip
        $this->authorize('showbookingsbytrip', $id);

        // Retrieve bookings for the specified trip
        $result = $this->BookingService->showbookingsbytrip($id);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? $this->paginated($result['data'], BookingResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Store a new booking in storage.
     *
     * @param StoreBookingRequest $request The request containing the booking data.
     * @return \Illuminate\Http\JsonResponse JSON response with the result of the booking creation.
     */
    public function store(StoreBookingRequest $request)
    {
        // Validate the incoming request data
        $validateddata = $request->validated();

        // Call the service to create a new booking
        $result = $this->BookingService->createBooking($validateddata);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Update a specific booking in storage.
     *
     * @param UpdateBookingRequest $request The request containing the updated booking data.
     * @param Booking $booking The booking to be updated.
     * @return \Illuminate\Http\JsonResponse JSON response with the result of the update.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        // Authorize the update action for the booking
        $this->authorize('update', $booking, Booking::class);

        // Validate the incoming request data
        $validateddata = $request->validated();

        // Call the service to update the booking
        $result = $this->BookingService->updateBooking($validateddata, $booking);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove a specific booking from storage.
     *
     * @param Booking $booking The booking to be deleted.
     * @return \Illuminate\Http\JsonResponse JSON response with the result of the deletion.
     */
    public function destroy(Booking $booking)
    {
        // Authorize the deletion action for the booking
        $this->authorize('delete', $booking, Booking::class);

        // Call the service to delete the booking
        $result = $this->BookingService->deleteBooking($booking);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Cancel a specific booking.
     *
     * @param Booking $booking The booking to be canceled.
     * @return \Illuminate\Http\JsonResponse JSON response with the result of the cancellation.
     */
    public function cancel(Booking $booking)
    {
        // Authorize the cancellation action for the booking
        $this->authorize('cancel', $booking, Booking::class);

        // Call the service to cancel the booking
        $result = $this->BookingService->cancelbooking($booking);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Accept a specific booking.
     *
     * @param Booking $booking The booking to be accepted.
     * @return \Illuminate\Http\JsonResponse JSON response with the result of the acceptance.
     */
    public function accept(Booking $booking)
    {
        // Authorize the acceptance action for the booking
        $this->authorize('accept', $booking, Booking::class);

        // Call the service to accept the booking
        $result = $this->BookingService->acceptedBooking($booking);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Reject a specific booking.
     *
     * @param Booking $booking The booking to be rejected.
     * @return \Illuminate\Http\JsonResponse JSON response with the result of the rejection.
     */
    public function reject(Booking $booking)
    {
        // Authorize the rejection action for the booking
        $this->authorize('reject', $booking, Booking::class);

        // Call the service to reject the booking
        $result = $this->BookingService->rejectBooking($booking);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
