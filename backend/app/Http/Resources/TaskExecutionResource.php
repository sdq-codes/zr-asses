<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskExecutionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'status' => $this->status,
            'urls_scraped' => $this->urls_scraped,
            'urls_success' => $this->urls_successful,
            'urls_failed' => $this->urls_failed,
            'execution_time_seconds' => $this->execution_time_seconds,
            'started_at' => $this->started_at?->toIso8601String(),
            'ended_at' => $this->completed_at?->toIso8601String(),
            'error_message' => $this->when($this->status === 'failed', $this->error_message),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
