<?php

namespace App\Providers;

use App\Repositories\Contracts\DestinationRepositoryInterface;
use App\Repositories\Contracts\OriginRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Implementations\RedisRepositories\RedisDestinationRepository;
use App\Repositories\Implementations\RedisRepositories\RedisOriginRepository;
use App\Repositories\Implementations\RedisRepositories\RedisTaskRepository;
use App\Repositories\Implementations\SqlRepositories\SqlDestinationRepository;
use App\Repositories\Implementations\SqlRepositories\SqlOriginRepository;
use App\Repositories\Implementations\SqlRepositories\SqlTaskRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        switch (config('database.default')) {
            case 'redis':
                $this->app->bind(OriginRepositoryInterface::class, RedisOriginRepository::class);
                $this->app->bind(DestinationRepositoryInterface::class, RedisDestinationRepository::class);
                $this->app->bind(TaskRepositoryInterface::class, RedisTaskRepository::class);
                break;
            default:
                $this->app->bind(OriginRepositoryInterface::class, SqlOriginRepository::class);
                $this->app->bind(DestinationRepositoryInterface::class, SqlDestinationRepository::class);
                $this->app->bind(TaskRepositoryInterface::class, SqlTaskRepository::class);
                break;
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
