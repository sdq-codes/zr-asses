<?php

namespace App\Services\Storage;

class StorageFactory
{
    public static function create(string $destination): StorageInterface
    {
        return match($destination) {
            'MongoDB' => new MongoDBStorage(),
            'MySQL' => new MySqlStorage(),
            'PostgreSQL' => new PostgreSQLStorage(),
            'AWS S3' => new S3Storage(),
            'Google Cloud Storage' => new GCSStorage(),
            'Redis' => new RedisStorage(),
            default => throw new \Exception("Unknown destination: {$destination}")
        };
    }
}
