<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Active Datastore
    |--------------------------------------------------------------------------
    |
    | This option controls the active datastore used by the application.
    | You can switch between 'mysql', 'postgresql', and 'redis'.
    |
    | Supported: "mysql", "postgresql", "redis"
    |
    */

    'active' => env('DATASTORE_ACTIVE', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Available Datastores
    |--------------------------------------------------------------------------
    |
    | Here you can define all available datastores for your application.
    | The application will validate against this list.
    |
    */

    'available' => [
        'mysql',
        'postgresql',
        'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Datastore Specific Configuration
    |--------------------------------------------------------------------------
    |
    | Additional configuration options for each datastore.
    |
    */

    'datastores' => [
        'mysql' => [
            'connection' => 'mysql',
            'supports_relations' => true,
            'supports_transactions' => true,
        ],

        'postgresql' => [
            'connection' => 'pgsql',
            'supports_relations' => true,
            'supports_transactions' => true,
        ],

        'redis' => [
            'connection' => 'redis',
            'supports_relations' => false,
            'supports_transactions' => false,
            'ttl' => 86400, // 24 hours default TTL
        ],
    ],
];
