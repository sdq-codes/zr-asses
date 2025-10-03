<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OriginService;
use App\Http\Requests\CreateOriginRequest;
use App\Http\Requests\UpdateOriginRequest;
use App\Http\Resources\OriginResource;
use App\Support\Enums\responses\InternalResponseCodes;

class OriginController extends Controller
{
    public function __construct(
        private readonly OriginService $originService
    ) {}

    /**
     * Display a listing of origins
     */
    public function index()
    {
        $origins = $this->originService->all();
        return response()->fetched(
            'Origins retrieved successfully',
            InternalResponseCodes::FETCHED_SUCCESS,
            OriginResource::collection($origins)
        );
    }

    /**
     * Store a newly created origin
     */
    public function store(CreateOriginRequest $request)
    {
        $requestData = $request->validated();
        $origin = $this->originService->create($requestData);

        return response()->created(
            'Origin created successfully',
            InternalResponseCodes::CREATED_SUCCESS,
            new OriginResource($origin)
        );
    }

    /**
     * Display the specified origin
     */
    public function show(string $id)
    {
        $origin = $this->originService->find($id);

        return response()->fetched(
            'Origin retrieved successfully',
            InternalResponseCodes::FETCHED_SUCCESS,
            new OriginResource($origin)
        );
    }

    /**
     * Update the specified origin
     */
    public function update(UpdateOriginRequest $request, string $id)
    {
        $this->originService->update($id, $request->validated());
        return response()->updated(
            'Origin updated successfully',
            InternalResponseCodes::UPDATED_SUCCESS,
            $this->originService->find($id)
        );
    }

    /**
     * Remove the specified origin
     */
    public function destroy(string $id)
    {
        $this->originService->delete($id);
        return response()->deleted(
            'Origin deleted successfully',
            InternalResponseCodes::DELETED_SUCCESS
        );
    }
}
