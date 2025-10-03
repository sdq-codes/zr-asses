<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskExecution extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'task_id',
        'started_at',
        'ended_at',
        'execution_time_ms',
        'urls_scraped',
        'urls_success',
        'urls_failed',
        'status',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'execution_time_ms' => 'integer',
        'urls_scraped' => 'integer',
        'urls_success' => 'integer',
        'urls_failed' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the task that owns the execution.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
