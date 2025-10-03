<?php

namespace App\Services;

use App\Exceptions\DeletingFailedException;
use App\Exceptions\ResourceException;
use App\Exceptions\UpdateFailedException;
use App\Models\Destination;
use App\Repositories\Contracts\DestinationRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class DestinationService
{
    protected DestinationRepositoryInterface $destinationRepository;

    public function __construct(
        DestinationRepositoryInterface $destinationRepository
    ) {
        $this->destinationRepository = $destinationRepository;
    }

    /**
     * Get all origins with pagination
     */
    public function all(): Collection
    {
        try {
            return $this->destinationRepository->all();
        } catch (Exception $e) {
            throw new ResourceException("Unable to fetch destinations", $e->getCode(), $e);
        }
    }

    /**
     * Find destination by id
     */
    public function find(string $id): Destination
    {
        try {
            return $this->destinationRepository->find($id);
        } catch (Exception $e) {
            throw new ResourceException("Destination with id $id not found", $e->getCode(), $e);
        }
    }

    /**
     * Create new Destination
     */
    public function create(array $data): Destination
    {
        try {
            return $this->destinationRepository->create($data);
        } catch (ResourceException $e) {
            throw new DeletingFailedException("Destination could not be created", $e->getCode(), $e);
        }
    }

    /**
     * Update existing origin
     */
    public function update(string $id, array $data): bool
    {
        try {
            return $this->destinationRepository->update($data, $id);
        } catch (ResourceNotFoundException $e) {
            throw new ResourceNotFoundException("Destination with id $id not found", $e->getCode(), $e);
        } catch (Exception $e) {
            throw new UpdateFailedException("Destination with id $id could not be updated", $e->getCode(), $e);
        }
    }

    /**
     * Delete destination
     */
    public function delete(string $id): bool
    {
        try {
            return $this->destinationRepository->delete($id);
        } catch (Exception|ResourceException|ResourceNotFoundException $e) {
            throw new DeletingFailedException("Destination with id $id could not be deleted", $e->getCode(), $e);
        }
    }
}
