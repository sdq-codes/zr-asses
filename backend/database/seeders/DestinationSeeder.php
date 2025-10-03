<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = [
            [
                'id'          => (string) Str::uuid(),
                'name' => 'MySQL',
                'image' => 'https://logo.clearbit.com/mysql.com',
                'description' => 'Store data in MySQL relational database',
            ],
            [
                'id'          => (string) Str::uuid(),
                'name' => 'PostgreSQL',
                'image' => 'https://logo.clearbit.com/postgresql.org',
                'description' => 'Store data in PostgreSQL database',
            ],
            [
                'id'          => (string) Str::uuid(),
                'name' => 'AWS S3',
                'image' => 'https://logo.clearbit.com/aws.amazon.com',
                'description' => 'Store files and data in Amazon S3 buckets',
            ],
            [
                'id'          => (string) Str::uuid(),
                'name' => 'MongoDB',
                'image' => 'https://logo.clearbit.com/mongodb.com',
                'description' => 'Store data in MongoDB NoSQL database',
            ],
            [
                'id'          => (string) Str::uuid(),
                'name' => 'Google Cloud Storage',
                'image' => 'https://logo.clearbit.com/cloud.google.com',
                'description' => 'Store data in Google Cloud Storage buckets',
            ],
            [
                'id'          => (string) Str::uuid(),
                'name' => 'Redis',
                'image' => 'https://logo.clearbit.com/redis.io',
                'description' => 'Store data in Redis in-memory data store',
            ],
        ];

        foreach ($destinations as $destination) {
            Destination::create($destination);
        }
    }
}
