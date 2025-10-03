<?php

namespace App\Services\Storage;

use App\Models\Task;

interface StorageInterface
{
    public function store(Task $task, array $urls): bool;
}
