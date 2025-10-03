<?php

namespace App\Repositories\Implementations\SqlRepositories;

use App\Models\TaskExecution;
use Illuminate\Database\Eloquent\Collection;

class SqlTaskExecutionRepository extends BaseRepository
{
    public function __construct(TaskExecution $model)
    {
        parent::__construct($model);
    }

    /**
     * Get executions for a specific task.
     */
    public function getByTask(int $taskId): Collection
    {
        return $this->model->where('task_id', $taskId)
            ->orderBy('started_at', 'desc')
            ->get();
    }

    /**
     * Get latest execution for a task.
     */
    public function getLatestForTask(int $taskId): ?TaskExecution
    {
        return $this->model->where('task_id', $taskId)
            ->orderBy('started_at', 'desc')
            ->first();
    }

    /**
     * Create a new execution.
     */
    public function startExecution(int $taskId): TaskExecution
    {
        return $this->create([
            'task_id' => $taskId,
            'started_at' => now(),
            'status' => 'running',
        ]);
    }

    /**
     * Get completed executions.
     */
    public function getCompleted(): Collection
    {
        return $this->model->completed()
            ->with('task')
            ->orderBy('started_at', 'desc')
            ->get();
    }

    /**
     * Get failed executions.
     */
    public function getFailed(): Collection
    {
        return $this->model->failed()
            ->with('task')
            ->orderBy('started_at', 'desc')
            ->get();
    }

    /**
     * Get executions within date range.
     */
    public function getByDateRange(\DateTime $startDate, \DateTime $endDate): Collection
    {
        return $this->model->whereBetween('started_at', [$startDate, $endDate])
            ->with('task')
            ->orderBy('started_at', 'desc')
            ->get();
    }

    /**
     * Get execution statistics for a task.
     */
    public function getTaskStatistics(int $taskId): array
    {
        $executions = $this->getByTask($taskId);

        return [
            'total_executions' => $executions->count(),
            'completed' => $executions->where('status', 'completed')->count(),
            'failed' => $executions->where('status', 'failed')->count(),
            'total_urls_scraped' => $executions->sum('urls_scraped'),
            'total_urls_success' => $executions->sum('urls_success'),
            'total_urls_failed' => $executions->sum('urls_failed'),
            'avg_execution_time_ms' => $executions->avg('execution_time_ms'),
            'success_rate' => $executions->count() > 0
                ? ($executions->where('status', 'completed')->count() / $executions->count()) * 100
                : 0,
        ];
    }
}
