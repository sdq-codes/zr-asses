<?php

namespace App\Services\Storage;

use App\Models\Task;
use MongoDB\Client;
use Carbon\Carbon;

class MongoDBStorage implements StorageInterface
{
    private $client;
    private $database;

    public function __construct()
    {
        $this->client = new Client(config('database.connections.mongodb.dsn'));
        $this->database = $this->client->selectDatabase(config('database.connections.mongodb.database'));
    }

    public function store(Task $task, array $urls): bool
    {
        $collection = $this->database->selectCollection('scraped_apartments');

        $document = [
            'task_id' => $task->id,
            'origin' => $task->origin->name,
            'urls' => $urls,
            'count' => count($urls),
            'scraped_at' => Carbon::now()->toISOString()
        ];

        $result = $collection->insertOne($document);

        return $result->getInsertedCount() > 0;
    }
}

