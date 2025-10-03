<?php

namespace App\Repositories\Implementations\RedisRepositories;

use App\Models\Task;
use App\Models\Origin;
use App\Models\Destination;
use App\Models\TaskExecution;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class RedisTaskRepository implements TaskRepositoryInterface
{
    protected string $prefix = 'task:';
    protected string $idsKey = 'tasks:ids';

    public function create(array $data): Task
    {
        $id = $data['id'] ?? (string) Str::uuid();
        $data['id'] = $id;

        Redis::set($this->prefix . $id, json_encode($data));

        return (new Task())->newFromBuilder($data);
    }

    public function find(string $id): ?Task
    {
        $data = Redis::get($this->prefix . $id);
        return $data ? $this->hydrate(json_decode($data, true)) : null;
    }

    public function all(): Collection
    {
        $ids = Redis::smembers($this->idsKey);

        $tasks = array_map(function ($id) {
            $data = Redis::get($this->prefix . $id);
            return $data ? $this->hydrate(json_decode($data, true)) : null;
        }, $ids);

        return new Collection(array_filter($tasks));
    }

    public function update(array $data, string $id): bool
    {
        $task = $this->find($id);
        if (! $task) return false;

        $updated = array_merge($task->toArray(), $data);
        Redis::set($this->prefix . $id, json_encode($updated));

        return true;
    }

    public function delete(string $id): bool
    {
        $deleted = Redis::del($this->prefix . $id);
        Redis::srem($this->idsKey, $id);

        return $deleted > 0;
    }

    public function getDueToRun(): Collection
    {
        return $this->all()->filter(fn(Task $task) =>
            isset($task->next_run_at) &&
            now()->greaterThanOrEqualTo($task->next_run_at)
        );
    }

    public function withRelations(): Collection
    {
        return $this->all()->map(fn(Task $task) => $this->hydrateRelations($task));
    }

    public function withLatestExecution(): Collection
    {
        return $this->all()->map(function (Task $task) {
            $this->hydrateRelations($task);
            $latest = $this->getLatestExecution($task->id);
            $task->setRelation('latestExecution', $latest);
            return $task;
        });
    }

    public function findByOriginAndDestination(string $originId, string $destinationId): ?Task
    {
        return $this->withRelations()->first(fn(Task $task) =>
            $task->origin_id == $originId && $task->destination_id == $destinationId
        );
    }

    public function getByOrigin(int $originId): Collection
    {
        return $this->withRelations()->filter(fn(Task $task) => $task->origin_id == $originId);
    }

    public function getByDestination(int $destinationId): Collection
    {
        return $this->withRelations()->filter(fn(Task $task) => $task->destination_id == $destinationId);
    }

    public function updateNextRunTime(string $id, \DateTime $nextRunAt): bool
    {
        return $this->update(['next_run_at' => $nextRunAt->format('Y-m-d H:i:s')], $id);
    }

    /**
     * -----------------------------
     * Private helpers
     * -----------------------------
     */

    private function hydrate(array $data): Task
    {
        return (new Task())->newFromBuilder($data);
    }

    private function hydrateRelations(Task $task): Task
    {
        $originData = Redis::get("origin:{$task->origin_id}");
        $destinationData = Redis::get("destination:{$task->destination_id}");

        if ($originData) {
            $task->setRelation('origin', new Origin(json_decode($originData, true)));
        }

        if ($destinationData) {
            $task->setRelation('destination', new Destination(json_decode($destinationData, true)));
        }

        return $task;
    }

    private function getLatestExecution(string $taskId): ?TaskExecution
    {
        $executionData = Redis::lindex("task:{$taskId}:executions", -1);
        return $executionData ? new TaskExecution(json_decode($executionData, true)) : null;
    }
}
