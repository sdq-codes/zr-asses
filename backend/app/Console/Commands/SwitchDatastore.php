<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SwitchDatastore extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'datastore:switch
                            {driver? : The datastore driver (mysql, redis, pgsql)}
                            {--migrate : Run migrations after switching}
                            {--sync : Sync data from current to new datastore}';

    /**
     * The console command description.
     */
    protected $description = 'Switch between different datastores (MySQL, Redis, PostgreSQL)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDriver = config('database.default');

        $driver = $this->argument('driver')
            ?? $this->choice(
                'Select the datastore you want to switch to',
                ['mysql', 'redis', 'pgsql'],
                array_search($currentDriver, ['mysql', 'redis', 'pgsql'])
            );

        $this->info("driver");
        $this->info($driver);

        if (!in_array($driver, ['mysql', 'redis', 'pgsql'])) {
            $this->error("Invalid driver: {$driver}");
            return 1;
        }

        if ($currentDriver === $driver) {
            $this->warn("Already using {$driver} datastore.");
            return 0;
        }

        $this->info("Switching from {$currentDriver} to {$driver}...");

        if (!$this->confirm("Are you sure you want to switch to {$driver}?", true)) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info("About to edit file");

        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('.env file not found!');
            return 1;
        }

        $envContent = file_get_contents($envPath);

        $this->info("DB_CONNECTION=$driver");

        if (preg_match('/^DB_CONNECTION=.*/m', $envContent)) {
            $envContent = preg_replace(
                '/^DB_CONNECTION=.*/m',
                "DB_CONNECTION=$driver",
                $envContent
            );
        } else {
            $envContent .= "\nDB_CONNECTION=$driver\n";
        }

        file_put_contents($envPath, $envContent);

        $this->info('Config cache cleared.');

        $this->newLine();
        $this->info("✓ Successfully switched to {$driver}!");
        $this->info("Current datastore: " . config('database.default'));
        Artisan::call('config:clear');

        return 0;
    }
}
