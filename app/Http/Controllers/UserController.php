<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Resources\UserProfileResource;

class UserController extends Controller
{
    /**
     * The UserService instance.
     *
     * @var UserService
     */
    protected $userService;

    /**
     * Create a new controller instance.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Retrieve a list of users based on the logged-in user's country and role.
     *
     * This method calls the `showUsers` method of the `UserService` to fetch users who:
     * - Have a profile with the same `country_id` as the logged-in user.
     * - Have the role `PrivateUser`.
     *
     * @return \Illuminate\Http\JsonResponse
     *   - If successful: Returns a JSON response with the list of users and a success message.
     *   - If an error occurs: Returns a JSON response with an error message and status code.
     */
    public function index()
    {
        // Call the UserService to fetch users
        $result = $this->userService->showUsers();

        // Return a JSON response based on the result status
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Retrieve profile details of a specific user.
     *
     * This method calls the `showUser` method of the `UserService` to fetch the profile details of a user, including:
     * - First name
     * - Last name
     * - Gender
     * - Phone number
     * - Address
     * - Country name
     *
     * @param User $user The user whose profile details are to be retrieved.
     * @return \Illuminate\Http\JsonResponse
     *   - If successful: Returns a JSON response with the profile details and a success message.
     *   - If an error occurs: Returns a JSON response with an error message and status code.
     */
    public function show( $id)
    {
        // Call the UserService to fetch user profile details
        $result = $this->userService->showUser($id);

        // Return a JSON response based on the result status
        return $result['status'] === 200
            ? $this->success(new UserProfileResource($result['data']), $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }
}
