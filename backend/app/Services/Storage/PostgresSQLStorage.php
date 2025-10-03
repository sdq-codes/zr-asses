<?php

namespace App\Services\Storage;

use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostgreSQLStorage implements StorageInterface
{
    public function store(Task $task, array $urls): bool
    {
        DB::connection('pgsql')->table('scraped_apartments')->insert([
            'task_id' => $task->id,
            'origin' => $task->origin->name,
            'urls' => json_encode($urls),
            'count' => count($urls),
            'scraped_at' => Carbon::now()
        ]);

        return true;
    }
}
