<?php

namespace App\Http\Controllers;

use App\Models\FavoritePerson;
use App\Services\FavoritePersonService;
use App\Http\Requests\FavoritPersonRequest\StorFavoritePersonRequest;
use App\Http\Resources\FavoriteUserResource;

class FavoritePersonController extends Controller
{
    // Property to hold the FavoritePersonService instance
    protected $favoritePersonService;

    /**
     * Constructor to inject the FavoritePersonService dependency.
     *
     * @param FavoritePersonService $favoritePersonService
     */
    public function __construct(FavoritePersonService $favoritePersonService)
    {
        $this->favoritePersonService = $favoritePersonService;
    }

    /**
     * Display a paginated list of favorite persons.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Retrieve the result from the FavoritePersonService
        $result = $this->favoritePersonService->showFavoritePerson();

        // Return a paginated response if the status is 200 (OK)
        // Otherwise, return an error response
        return $result['status'] === 200
            ? $this->paginated($result['data'], FavoriteUserResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Store a newly created favorite person in storage.
     *
     * @param StorFavoritePersonRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorFavoritePersonRequest $request)
    {
        // Validate the incoming request data
        $validationData = $request->validated();

        // Uncomment the following line to authorize the action if needed
        // $this->authorize('addfavorite', $validationData["user_id"]);

        // Add the favorite person using the FavoritePersonService
        $result = $this->favoritePersonService->addToFavorite($validationData);

        // Return a success response if the status is 201 (Created)
        // Otherwise, return an error response
        return $result['status'] === 201
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove the specified favorite person from storage.
     *
     * @param FavoritePerson $favoritePerson
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(FavoritePerson $favoritePerson)
    {
        // Uncomment the following line to authorize the action if needed
        // $this->authorize('delete', $favoritePerson);

        // Remove the favorite person using the FavoritePersonService
        $result = $this->favoritePersonService->removeFromFavorite($favoritePerson);

        // Return a success response if the status is 200 (OK)
        // Otherwise, return an error response
        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
