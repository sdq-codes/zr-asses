<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Support\Enums\responses\InternalResponseCodes;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {}

    /**
     * Display a listing of tasks
     */
    public function index(Request $request)
    {
        $tasks = $this->taskService->all();
        return response()->fetched(
            'Tasks retrieved successfully',
            InternalResponseCodes::FETCHED_SUCCESS,
            TaskResource::collection($tasks),
        );
    }

    /**
     * Store a newly created task
     */
    public function store(CreateTaskRequest $request)
    {
        $requestData = $request->validated();
        $task = $this->taskService->create($requestData);

        return response()->created(
            'Task created successfully',
            InternalResponseCodes::CREATED_SUCCESS,
            new TaskResource($task)
        );
    }

    /**
     * Display the specified task
     */
    public function show(string $id)
    {
        $task = $this->taskService->find($id);

        return response()->fetched(
            'Task retrieved successfully',
            InternalResponseCodes::FETCHED_SUCCESS,
            new TaskResource($task)
        );
    }

    /**
     * Update the specified task
     */
    public function update(UpdateTaskRequest $request, string $id)
    {
        $this->taskService->update($id, $request->validated());

        return response()->updated(
            'Task updated successfully',
            InternalResponseCodes::UPDATED_SUCCESS,
        );
    }

    /**
     * Remove the specified task
     */
    public function destroy(string $id)
    {
        $this->taskService->delete($id);

        return response()->deleted(
            'Task deleted successfully',
            InternalResponseCodes::DELETED_SUCCESS
        );
    }
}
