<?php

namespace App\Services\Storage;

use App\Models\Task;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class RedisStorage implements StorageInterface
{
    public function store(Task $task, array $urls): bool
    {
        $key = sprintf('scraped:%s:%s', $task->origin->name, Carbon::now()->timestamp);

        $data = [
            'task_id' => $task->id,
            'origin' => $task->origin->name,
            'urls' => $urls,
            'count' => count($urls),
            'scraped_at' => Carbon::now()->toISOString()
        ];

        Redis::setex($key, 86400 * 7, json_encode($data));

        return true;
    }
}
