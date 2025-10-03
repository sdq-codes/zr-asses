<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OriginController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('v1.')->group(function () {

// Origins routes
    Route::prefix('origins')->name('origins.')->group(function () {
        Route::get('/', [OriginController::class, 'index'])->name('index');
        Route::post('/', [OriginController::class, 'store'])->name('store');
        Route::get('/stats', [OriginController::class, 'stats'])->name('stats');
        Route::get('/{id}', [OriginController::class, 'show'])->name('show');
        Route::patch('/{id}', [OriginController::class, 'update'])->name('update');
        Route::delete('/{id}', [OriginController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [OriginController::class, 'toggleStatus'])->name('toggle-status');
    });

    Route::prefix('destinations')->name('destinations.')->group(function () {
        Route::get('/', [DestinationController::class, 'index'])->name('index');
        Route::post('/', [DestinationController::class, 'store'])->name('store');
        Route::get('/search', [DestinationController::class, 'search'])->name('search');
        Route::get('/stats', [DestinationController::class, 'stats'])->name('stats');
        Route::get('/{id}', [DestinationController::class, 'show'])->name('show');
        Route::patch('/{id}', [DestinationController::class, 'update'])->name('update');
        Route::delete('/{id}', [DestinationController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [DestinationController::class, 'toggleStatus'])->name('toggle-status');
    });

// Tasks routes
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/search', [TaskController::class, 'search'])->name('search');
        Route::get('/stats', [TaskController::class, 'stats'])->name('stats');
        Route::get('/ready-for-execution', [TaskController::class, 'readyForExecution'])->name('ready-for-execution');
        Route::get('/last-selection', [TaskController::class, 'getLastSelection'])->name('last-selection');
        Route::post('/save-selection', [TaskController::class, 'saveSelection'])->name('save-selection');
        Route::get('/{id}', [TaskController::class, 'show'])->name('show');
        Route::patch('/{id}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{id}', [TaskController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [TaskController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{id}/schedule', [TaskController::class, 'schedule'])->name('schedule');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('api.home');

});

// Public routes

// Health check route
Route::get('/health', function () {
    return response()->fetched(
        'API is healthy',
        'HEALTH_CHECK_SUCCESS',
        [
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
            'environment' => app()->environment(),
        ]
    );
})->name('health');

// Version route
Route::get('/version', function () {
    return response()->fetched(
        'API version information',
        'VERSION_INFO_SUCCESS',
        [
            'version' => config('app.version', '1.0.0'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
        ]
    );
})->name('version');
