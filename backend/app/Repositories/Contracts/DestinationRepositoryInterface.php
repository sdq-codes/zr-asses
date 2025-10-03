<?php

namespace App\Repositories\Contracts;

use App\Models\Destination;
use Illuminate\Support\Collection;

interface DestinationRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): Destination;
    public function create(array $data): Destination;
    public function update(array $data, string $id): bool;
    public function delete(string $id): bool;
}
