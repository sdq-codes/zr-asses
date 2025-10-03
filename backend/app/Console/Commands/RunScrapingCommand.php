<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\TaskExecution;
use App\Services\ScraperEngine\ApartmentUrlScrapper;
use App\Services\Storage\StorageFactory;
use Cron\CronExpression;
use Illuminate\Console\Command;
use Carbon\Carbon;

class RunScrapingCommand extends Command
{
    protected $signature = 'scraping:run';
    protected $description = 'Run all active scraping tasks';

    public function handle()
    {
        $tasks = Task::where('next_run_at', '<=', Carbon::now())->get();

        if ($tasks->isEmpty()) {
            $this->info('No due tasks found');
            return;
        }

        $this->info("Found {$tasks->count()} active task(s)");

        foreach ($tasks as $task) {
            $this->processTask($task);
        }

        $this->info('All tasks completed');
    }

    private function processTask(Task $task)
    {
        $this->info("Processing task: {$task->id}");

        $execution = TaskExecution::create([
            'task_id' => $task->id,
            'started_at' => Carbon::now(),
            'status' => 'running'
        ]);

        try {
            $scraper = new ApartmentUrlScrapper();
            $originUrl = $task->origin->url;

            $result = $scraper->scrape($originUrl);

            if ($result['success']) {
                // Store the scraped data
                $storage = StorageFactory::create($task->destination->name);
                $storage->store($task, $result['urls']);

                $execution->update([
                    'ended_at' => Carbon::now(),
                    'execution_time_ms' => $result['execution_time_ms'],
                    'urls_scraped' => $result['count'],
                    'urls_success' => $result['count'],
                    'urls_failed' => 0,
                    'status' => 'completed',
                    'metadata' => [
                        'origin' => $task->origin->name,
                        'destination' => $task->destination->name,
                        'url_scraped' => $originUrl
                    ]
                ]);

                $task->update(['last_executed_at' => Carbon::now()]);

                $this->info("✓ Task {$task->id} completed: {$result['count']} URLs scraped");
            } else {
                throw new \Exception($result['error']);
            }

        } catch (\Exception $e) {
            $execution->update([
                'ended_at' => Carbon::now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'metadata' => [
                    'origin' => $task->origin->name,
                    'destination' => $task->destination->name
                ]
            ]);

            $this->error("✗ Task {$task->id} failed: {$e->getMessage()}");
        } finally {
            $task->last_run_at = $task->next_run_at;

            $cron = CronExpression::factory($task->schedule_expression);
            $task->next_run_at = $cron->getNextRunDate();

            $task->save();
        }
    }
}
