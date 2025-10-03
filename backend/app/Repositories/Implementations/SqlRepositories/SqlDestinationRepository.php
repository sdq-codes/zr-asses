<?php

namespace App\Repositories\Implementations\SqlRepositories;

use App\Models\Destination;
use App\Repositories\Contracts\DestinationRepositoryInterface;
use App\Traits\UpdateModel;
use Illuminate\Database\Eloquent\Collection;

class SqlDestinationRepository extends BaseRepository implements DestinationRepositoryInterface
{
    use UpdateModel;

    protected array $updatableFields = [
        'name' => 'name',
        'image' => 'image',
        'description' => 'description',
    ];

    public function __construct(Destination $model)
    {
        parent::__construct($model);
    }

    /**
     * Fetch all destinations.
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Fetch Destination by iD
     */
    public function find(string $id): Destination
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new Destination
     */
    public function create(array $data): Destination
    {
        return $this->model->create($data);
    }

    /**
     * Update existing Destination
     */
    public function update(array $data, string $id): bool
    {
        $destination = $this->find($id);
        return $this->updateModelAttributes($destination, $data, $this->updatableFields)->save();
    }

    /**
     * Delete an Destination.
     */
    public function delete(string $id): bool
    {
        return (bool) $this->model->where("id", $id)->delete();
    }
}
