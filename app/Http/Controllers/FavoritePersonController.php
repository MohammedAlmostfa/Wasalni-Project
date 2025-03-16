<?php

namespace App\Http\Controllers;

use App\Models\FavoritePerson;
use App\Services\FavoritePersonService;
use App\Http\Requests\FavoritPersonRequest\StorFavoritePersonRequest;
use App\Http\Resources\FavoriteUserResource;

class FavoritePersonController extends Controller
{
    protected $favoritePersonService;

    public function __construct(FavoritePersonService $favoritePersonService)
    {
        $this->favoritePersonService = $favoritePersonService;
    }

    public function index()
    {

        $result = $this->favoritePersonService->showFavoritePerson();
        return $result['status'] === 200
                  ?$this->paginated($result['data'], FavoriteUserResource::class, $result['message'], $result['status'])
                  : self::error(null, $result['message'], $result['status']);

    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorFavoritePersonRequest $request)
    {
        $validationData = $request->validated();

        // $this->authorize('addfavorite', $validationData["user_id"]);

        $result = $this->favoritePersonService->addToFavorite($validationData);
        return $result['status'] === 201
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FavoritePerson $favoritePerson)
    {
        //$this->authorize('delete', $favoritePerson);

        $result = $this->favoritePersonService->removeFromFavorite($favoritePerson);

        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
