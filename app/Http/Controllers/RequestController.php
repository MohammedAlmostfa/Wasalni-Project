<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Services\RequestService;
use App\Http\Requests\RequestRequest\StoreRequesData;
use App\Http\Requests\RequestRequest\UpdateRequestData;

class RequestController extends Controller
{
    /**
     * The request service that contains the business logic.
     *
     * @var RequestService
     */
    protected $requestService;

    /**
     * Constructor: injects the request service.
     *
     * @param RequestService $requestService
     */
    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    /**
     * Show all requests (not implemented yet).
     */
    public function index()
    {
        //
    }

    /**
     * Show the form to create a new request (not implemented yet).
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created request in the database.
     *
     * @param StoreRequesData $request The validated request data
     * @return \Illuminate\Http\JsonResponse Success or error response
     */
    public function store(StoreRequesData $request)
    {
        $validationData = $request->validated(); // Get validated input

        $result = $this->requestService->createRequest($validationData); // Handle saving logic

        // Return success or error response based on result
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Update the status of an existing request (e.g., accept or reject).
     *
     * @param UpdateRequestData $requestdata Contains the new validated status value
     * @param Request $request The request model instance to be updated
     * @return \Illuminate\Http\JsonResponse Success or error response
     */
    public function update(UpdateRequestData $requestdata, Request $request)
    {
        $this->authorize('update', $request); // Check user authorization

        $validationData = $requestdata->validated(); // Validate input

        $result = $this->requestService->updataStatus($validationData, $request); // Perform update

        // Return proper response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Show the form to edit an existing request (not implemented yet).
     *
     * @param Request $request
     */
    public function edit(Request $request)
    {
        //
    }

    /**
     * Delete an existing request (not implemented yet).
     *
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        //
    }
}
