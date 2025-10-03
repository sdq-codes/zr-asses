<?php

namespace App\Repositories\Contracts;

use App\Models\Origin;
use Illuminate\Support\Collection;

interface OriginRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): Origin;
    public function create(array $data): Origin;
    public function update(array $data, string $id): bool;
    public function delete(string $id): bool;
}
