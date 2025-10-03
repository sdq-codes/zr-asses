<?php

use Illuminate\Support\Str;

/*
 * Docker Database Configuration Override
 * This file provides additional database connection configurations for Docker environment
 */

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'mysql'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel_app'),
            'username' => env('DB_USERNAME', 'laravel_user'),
            'password' => env('DB_PASSWORD', 'laravel_password'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_PGSQL_URL'),
            'host' => env('DB_PGSQL_HOST', 'postgres'),
            'port' => env('DB_PGSQL_PORT', '5432'),
            'database' => env('DB_PGSQL_DATABASE', 'laravel_app_pg'),
            'username' => env('DB_PGSQL_USERNAME', 'laravel_user_pg'),
            'password' => env('DB_PGSQL_PASSWORD', 'laravel_password_pg'),
            'charset' => env('DB_PGSQL_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'redis' => [
            'client' => env('REDIS_CLIENT', 'phpredis'),
            'options' => [
                'cluster' => env('REDIS_CLUSTER', 'redis'),
                'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
            ],
            'default' => [
                'url' => env('REDIS_URL'),
                'host' => env('REDIS_HOST', 'redis'),
                'username' => env('REDIS_USERNAME'),
                'password' => env('REDIS_PASSWORD'),
                'port' => env('REDIS_PORT', '6379'),
                'database' => env('REDIS_DB', '0'),
            ],
            'cache' => [
                'url' => env('REDIS_URL'),
                'host' => env('REDIS_HOST', 'redis'),
                'username' => env('REDIS_USERNAME'),
                'password' => env('REDIS_PASSWORD'),
                'port' => env('REDIS_PORT', '6379'),
                'database' => env('REDIS_CACHE_DB', '1'),
            ],
        ],
    ],
];