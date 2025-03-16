<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

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




}
