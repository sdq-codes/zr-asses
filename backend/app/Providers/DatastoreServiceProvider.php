<?php

namespace App\Providers;

use App\Models\Destination;
use App\Models\Origin;
use App\Models\Task;
use App\Models\TaskExecution;
use App\Repositories\{Implementations\SqlRepositories\SqlDestinationRepository,
    Implementations\SqlRepositories\SqlOriginRepository,
    Implementations\SqlRepositories\SqlTaskExecutionRepository,
    Implementations\SqlRepositories\SqlTaskRepository};
use Illuminate\Support\ServiceProvider;

class DatastoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Repositories - they will use the active datastore
        $this->app->singleton(SqlOriginRepository::class, function ($app) {
            return new SqlOriginRepository(new Origin());
        });

        $this->app->singleton(SqlDestinationRepository::class, function ($app) {
            return new SqlDestinationRepository(new Destination());
        });

        $this->app->singleton(SqlTaskRepository::class, function ($app) {
            return new SqlTaskRepository(new Task());
        });

        $this->app->singleton(SqlTaskExecutionRepository::class, function ($app) {
            return new SqlTaskExecutionRepository(new TaskExecution());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $datastoreManager = $this->app->make(DatastoreManager::class);
        $activeDatastore = config('datastore.active', 'mysql');
        $datastoreManager->setActive($activeDatastore);
    }
}
