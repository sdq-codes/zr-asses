<?php

namespace App\Services;

use App\Exceptions\DeletingFailedException;
use App\Exceptions\ResourceException;
use App\Exceptions\UpdateFailedException;
use App\Models\Origin;
use App\Repositories\Contracts\OriginRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;

class OriginService
{
    protected OriginRepositoryInterface $originRepository;

    protected UrlMetadataService $metadataService;

    public function __construct(
        OriginRepositoryInterface $originRepository,
        UrlMetadataService $metadataService
    ) {
        $this->originRepository = $originRepository;
        $this->metadataService = $metadataService;
    }

    /**
     * Get all origins with pagination
     */
    public function all(): Collection
    {
        try {
            return $this->originRepository->all();
        } catch (Exception $e) {
            throw new ResourceException("Unable to fetch origins", $e->getCode(), $e);
        }
    }

    /**
     * Find origin by id
     */
    public function find(string $id): Origin
    {
        try {
            return $this->originRepository->find($id);
        } catch (Exception $e) {
            throw new ResourceException("Origin with id $id not found", $e->getCode(), $e);
        }
    }

    /**
     * Create new Origin
     */
    public function create(array $data): Origin
    {
        try {
            return $this->originRepository->create($data);
        } catch (ResourceException $e) {
            throw new DeletingFailedException("Origin could not be created", $e->getCode(), $e);
        }
    }

    /**
     * Update existing origin
     */
    public function update(string $id, array $data): bool
    {
        try {
            return $this->originRepository->update($data, $id);
        } catch (ResourceException $e) {
            throw new UpdateFailedException("Origin with id $id could not be updated", $e->getCode(), $e);
        }
    }

    /**
     * Delete origin
     */
    public function delete(string $id): bool
    {
        try {
            return $this->originRepository->delete($id);
        } catch (ResourceException $e) {
            throw new DeletingFailedException("Origin with id $id could not be deleted", $e->getCode(), $e);
        }
    }
}
