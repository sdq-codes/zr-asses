<?php

namespace App\Repositories\Implementations\RedisRepositories;

use App\Models\Destination;
use App\Repositories\Contracts\DestinationRepositoryInterface;
use App\Support\Helpers\UuidHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class RedisDestinationRepository implements DestinationRepositoryInterface
{
    private string $prefix = 'destinations:';

    public function all(): Collection
    {
        $keys = Redis::keys($this->prefix . '*');
        $destinations = [];

        foreach ($keys as $key) {
            $uuid = UuidHelper::extractOne($key);
            $data = Redis::get($this->prefix . $uuid);
            if ($data) {
                $attributes = json_decode($data, true);
                $destinations[] = (new Destination())->newFromBuilder($attributes);
            }
        }

        return collect($destinations);
    }

    public function find($id): Destination
    {
        $data = Redis::get($this->prefix . $id);
        if (!$data) {
            throw new \Exception('Destination not found');
        }

        $attributes = json_decode($data, true);

        return (new Destination())->newFromBuilder($attributes);
    }

    public function create(array $data): Destination
    {
        $id = $data['id'] ?? Str::uuid()->toString();
        $data['id'] = $id;
        $data['created_at'] = now()->toDateTimeString();
        $data['updated_at'] = now()->toDateTimeString();

        Redis::set($this->prefix . $id, json_encode($data), 'NX');
        return (new Destination())->newFromBuilder($data);
    }

    /**
     * @throws \Exception
     */
    public function update(array $data, string $id): bool
    {
        $existing = $this->find($id);
        if (!$existing) {
            throw new ResourceNotFoundException('Destination not found');
        }

        $updated = array_merge($existing->toArray(), $data);
        $updated['updated_at'] = now()->toDateTimeString();

        return (bool)Redis::set($this->prefix . $id, json_encode($updated));
    }

    public function delete($id): bool
    {
        $this->find($id);
        return Redis::del($this->prefix . $id) > 0;
    }
}
