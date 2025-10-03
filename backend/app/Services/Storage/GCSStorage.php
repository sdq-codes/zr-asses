<?php

namespace App\Services\Storage;

use App\Models\Task;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GCSStorage implements StorageInterface
{
    public function store(Task $task, array $urls): bool
    {
        $filename = sprintf(
            'scraped-data/%s/%s.json',
            $task->origin->name,
            Carbon::now()->format('Y-m-d_H-i-s')
        );

        $data = [
            'task_id' => $task->id,
            'origin' => $task->origin->name,
            'urls' => $urls,
            'count' => count($urls),
            'scraped_at' => Carbon::now()->toISOString()
        ];

        return Storage::disk('gcs')->put($filename, json_encode($data, JSON_PRETTY_PRINT));
    }
}
