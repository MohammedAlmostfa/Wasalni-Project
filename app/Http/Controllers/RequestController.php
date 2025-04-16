<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Services\RequestService;
use App\Http\Requests\RequestRequest\StoreRequesData;

class RequestController extends Controller
{
    /**
     * Inject the RequestService dependency.
     *
     * @param RequestService $requestService
     */
    protected $requestService;
    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created request.
     *
     * @param StoreRequesData $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequesData $request)
    {
        $validationData = $request->validated();

        $result = $this->requestService->createRequest($validationData);

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    public function show(Request $request)
    {
        //
    }

    public function edit(Request $request)
    {
        //
    }

    public function destroy(Request $request)
    {
        //
    }
}
