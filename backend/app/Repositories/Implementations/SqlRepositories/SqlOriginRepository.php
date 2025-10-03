<?php

namespace App\Repositories\Implementations\SqlRepositories;

use App\Models\Origin;
use App\Repositories\Contracts\OriginRepositoryInterface;
use App\Traits\UpdateModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class SqlOriginRepository extends BaseRepository implements OriginRepositoryInterface
{
    use UpdateModel;
    protected array $updatableFields = [
        'name' => 'name',
        'image' => 'image',
        'description' => 'description',
    ];

    public function __construct(Origin $model)
    {
        parent::__construct($model);
    }

    /**
     * Fetch all origins.
     */
    public function all(): Collection
    {
        Log::debug('This is a debug message');
        return $this->model->all();
    }

    /**
     * Fetch Origin by iD
     */
    public function find(string $id): Origin
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new Origin
     */
    public function create(array $data): Origin
    {
        return $this->model->create($data);
    }

    /**
     * Update existing Origin
     */
    public function update(array $data, string $id): bool
    {
        $origin = $this->find($id);
        return $this->updateModelAttributes($origin, $data, $this->updatableFields)->save();
    }

    /**
     * Delete an Origin.
     */
    public function delete(string $id): bool
    {
        return (bool) $this->model->findOrFail($id)->delete();
    }
}
