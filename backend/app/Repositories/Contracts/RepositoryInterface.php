<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * Get all records.
     */
    public function all(): Collection;

    /**
     * Find a record by ID.
     */
    public function find(string $id): Model;

    /**
     * Create a new record.
     */
    public function create(array $data): Model;

    /**
     * Update a record.
     */
    public function update(array $data, string $id): bool;

    /**
     * Delete a record.
     */
    public function delete(string $id): bool;
}
