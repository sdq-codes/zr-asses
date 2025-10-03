<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TaskRepositoryInterface
{
    public function create(array $data): Task;
    public function all(): Collection;
    public function find(string $id): ?Task;
    public function update(array $data, string $id): bool;
    public function delete(string $id): bool;
    public function withRelations(): Collection;
    public function findByOriginAndDestination(string $originId, string $destinationId): ?Task;
    public function getDueToRun(): Collection;
    public function getByOrigin(string $originId): Collection;
    public function getByDestination(int $destinationId): Collection;
    public function updateNextRunTime(int $id, \DateTime $nextRunAt): bool;
}
