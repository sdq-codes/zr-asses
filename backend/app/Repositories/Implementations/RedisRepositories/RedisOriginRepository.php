<?php

namespace App\Repositories\Implementations\RedisRepositories;

use App\Models\Origin;
use App\Repositories\Contracts\OriginRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use App\Support\Helpers\UuidHelper;

class RedisOriginRepository implements OriginRepositoryInterface
{
    protected string $prefix = 'origins:';

    public function all(): Collection
    {
        $keys = Redis::keys($this->prefix . '*');
        $origins = [];

        foreach ($keys as $key) {
            $uuid = UuidHelper::extractOne($key);
            $data = Redis::get($this->prefix . $uuid);
            if ($data) {
                $attributes = json_decode($data, true);

                $origins[] = (new Origin())->newFromBuilder($attributes);
            }
        }
        return collect($origins);
    }

    public function find($id): Origin
    {
        $data = Redis::get($this->prefix . $id);
        if (!$data) {
            throw new ModelNotFoundException('Origin not found');
        }

        $attributes = json_decode($data, true);

        return (new Origin())->newFromBuilder($attributes);
    }

    public function create(array $data): Origin
    {
        $id = $data['id'] ?? Str::uuid()->toString();
        $data['id'] = $id;
        $data['created_at'] = now()->toDateTimeString();
        $data['updated_at'] = now()->toDateTimeString();

        Redis::set($this->prefix . $id, json_encode($data), 'NX');
        Redis::sadd($this->idsKey, $id);
        return (new Origin())->newFromBuilder($data);
    }

    /**
     * @throws \Exception
     */
    public function update(array $data, string $id): bool
    {
        $existing = $this->find($id);
        if (!$existing) {
            throw new \Exception('Origin not found');
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
