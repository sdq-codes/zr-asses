<?php

namespace App\Services;

use App\Repositories\Contracts\TaskRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\ResourceException;
use Cron\CronExpression;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly OriginService           $originService,
        private readonly DestinationService $destinationService
    ) {}

    /**
     * Get all tasks with pagination
     */
    public function all(): Collection
    {
        return $this->taskRepository->withRelations();
    }

    /**
     * Find task by ID
     */
    public function find(string $id)
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            throw new ResourceException("Task with $id not found");
        }
        return $task;
    }

    /**
     * Create new task
     */
    public function create(array $data): mixed
    {
        $this->originService->find($data['origin_id']);

        $this->destinationService->find($data['destination_id']);
        $cron = new CronExpression($data['schedule_expression']);
        $data['next_run_at'] = $cron->getNextRunDate();
        return $this->taskRepository->create($data);
    }

    /**
     * Update existing task
     */
    public function update(string $id, array $data): mixed
    {
        try {
            // Validate origin if provided
            if (isset($data['origin_id'])) {
                $this->originService->find($data['origin_id']);
            }

            // Validate destination if provided
            if (isset($data['destination_id'])) {
                $this->destinationService->find($data['destination_id']);
            }

            // Check if task with same combination already exists (excluding current)
            if (isset($data['origin_id']) && isset($data['destination_id'])) {
                if ($this->taskRepository->findByOriginAndDestination(
                    $data['origin_id'],
                    $data['destination_id'],
                )) {
                    throw new ResourceNotFoundException('Task with this Origin and Destination combination');
                }
            }
            return $this->taskRepository->update($data, $id);
        } catch (ResourceException|Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete task
     * @throws Exception
     */
    public function delete(string $id): bool
    {
        try {
            return $this->taskRepository->delete($id);
        } catch (ResourceNotFoundException $e) {
            throw new ResourceNotFoundException("Task with id $id not found");
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
