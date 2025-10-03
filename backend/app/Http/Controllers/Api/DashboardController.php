<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Services\OriginService;
use App\Services\DestinationService;
use App\Services\TaskService;
use App\Http\Resources\OriginResource;
use App\Http\Resources\DestinationResource;
use App\Support\Enums\responses\InternalResponseCodes;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected OriginService $originService;
    protected DestinationService $destinationService;
    protected TaskService $taskService;
    public function __construct(
        OriginService $originService,
        DestinationService $destinationService,
        TaskService $taskService
    ) {
        $this->originService = $originService;
        $this->destinationService = $destinationService;
        $this->taskService = $taskService;
    }

    /**
     * Get homepage data (Origins and Destinations)
     */
    public function index(Request $request)
    {
        $origins = $this->originService->all();
        $destinations = $this->destinationService->all();
        $tasks = $this->taskService->all();



        return response()->fetched(
            'Dashboard data retrieved successfully',
            InternalResponseCodes::FETCHED_SUCCESS,
            [
                'origins' => OriginResource::collection($origins),
                'destinations' => DestinationResource::collection($destinations),
                'tasks' => TaskResource::collection($tasks),
            ]
        );
    }
}
