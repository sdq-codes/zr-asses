<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDestinationRequest;
use App\Http\Requests\UpdateDestinationRequest;
use App\Http\Resources\DestinationResource;
use App\Services\DestinationService;
use App\Support\Enums\responses\InternalResponseCodes;

class DestinationController extends Controller
{

    public function __construct(
        private readonly DestinationService $destinationService
    ) {}

    /**
     * Display a listing of destinations
     */
    public function index()
    {
        $destinations = $this->destinationService->all();

        return response()->fetched(
            'Destinations retrieved successfully',
            InternalResponseCodes::FETCHED_SUCCESS,
            DestinationResource::collection($destinations),
        );
    }

    /**
     * Store a newly created destination
     */
    public function store(CreateDestinationRequest $request)
    {
        $destination = $this->destinationService->create($request->validated());

        return response()->created(
            'Destination created successfully',
            InternalResponseCodes::CREATED_SUCCESS,
            new DestinationResource($destination)
        );
    }

    /**
     * Display the specified destination
     */
    public function show(string $id)
    {
        $destination = $this->destinationService->find($id);

        return response()->fetched(
            'Destination retrieved successfully',
            InternalResponseCodes::FETCHED_SUCCESS,
            new DestinationResource($destination)
        );
    }

    /**
     * Update the specified destination
     */
    public function update(UpdateDestinationRequest $request, string $id)
    {
        $this->destinationService->update($id, $request->validated());

        return response()->updated(
            'Destination updated successfully',
            InternalResponseCodes::UPDATED_SUCCESS,
        );
    }

    /**
     * Remove the specified destination
     */
    public function destroy(string $id)
    {
        $this->destinationService->delete($id);

        return response()->deleted(
            'Destination deleted successfully',
            InternalResponseCodes::DELETED_SUCCESS
        );
    }

}
