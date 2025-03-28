<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest\UserDeviceRequest;
use App\Services\UserDeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDeviceController extends Controller
{
    // Declare the UserDeviceService property for dependency injection
    protected $UserDeviceService;

    /**
     * Inject the UserDeviceService through the constructor.
     *
     * @param UserDeviceService $UserDeviceService
     */
    public function __construct(UserDeviceService $UserDeviceService)
    {
        // Assign the injected service to the class property
        $this->UserDeviceService = $UserDeviceService;
    }

    /**
     * Store or update the user's device FCM token.
     *
     * @param UserDeviceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserDeviceRequest $request)
    {
        // Validate the incoming request data using the custom form request validation rules
        $validationData = $request->validated();

        // Call the createUserDevice method from the service to handle the device creation or update logic
        $result = $this->UserDeviceService->createUserDevice($validationData);

        // Return a success or error response based on the result
        return $result['status'] === 200
            ? $this->success(null, $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }
}
