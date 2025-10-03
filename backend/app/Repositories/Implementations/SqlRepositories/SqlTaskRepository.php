<?php

namespace App\Repositories\Implementations\SqlRepositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Traits\UpdateModel;
use Illuminate\Database\Eloquent\Collection;

class SqlTaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    use UpdateModel;

    protected array $taskUpdatableFields = [
        'destination_id' => 'destination_id' ,
    ];

    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    /**
     * Get tasks with origin and destination.
     */
    public function withRelations(): Collection
    {
        return $this->baseQuery()->get();
    }

    /**
     * Find task by origin and destination.
     */
    public function findByOriginAndDestination(string $originId, string $destinationId): ?Task
    {
        return $this->model
            ->where('origin_id', $originId)
            ->where('destination_id', $destinationId)
            ->first();
    }

    /**
     * Create or update task.
     */
    public function create(array $data): Task
    {
        return $this->model->updateOrCreate(
            [
                'origin_id' => $data["origin_id"],
                'destination_id' => $data["destination_id"],
            ],
            $data
        );
    }

    /**
     * Get tasks that are due to run.
     */
    public function getDueToRun(): Collection
    {
        return $this->model->dueToRun()
            ->with(['origin', 'destination'])
            ->get();
    }

    /**
     * Get tasks with latest execution.
     */
    public function withLatestExecution(): Collection
    {
        return $this->baseQuery()->with('latestExecution')->get();
    }

    /**
     * Get tasks by origin.
     */
    public function getByOrigin(string $originId): Collection
    {
        return $this->baseQuery()->where('origin_id', $originId)->get();
    }

    /**
     * Get tasks by destination.
     */
    public function getByDestination(int $destinationId): Collection
    {
        return $this->baseQuery()->where('destination_id', $destinationId)
            ->get();
    }

    /**
     * Update next run time.
     */
    public function updateNextRunTime(int $id, \DateTime $nextRunAt): bool
    {
        return $this->update($id, ['next_run_at' => $nextRunAt]);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(string $id): Task
    {
        return $this->model->findOrFail($id);
    }

    public function update(array $data, string $id): bool
    {
        $task = $this->find($id);
        $this->updateModelAttributes($task, $data, $this->taskUpdatableFields);
        return $task->save();
    }

    /**
     * Delete an Origin.
     */
    public function delete(string $id): bool
    {
        return (bool) $this->model->where("id", $id)->delete();
    }

    private function baseQuery()
    {
        return $this->model->with(['origin', 'destination']);
    }
}
