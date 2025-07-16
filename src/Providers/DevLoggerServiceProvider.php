<?php

namespace DevLoggerPackage\Providers;

use DevLoggerPackage\Console\Commands\CleanupLogsCommand;
use DevLoggerPackage\Services\DeveloperLogService;
use Illuminate\Support\ServiceProvider;

class DevLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/devlogger.php',
            'devlogger'
        );

        // Register DeveloperLogService as a singleton
        $this->app->singleton('devlogger', function ($app) {
            return new DeveloperLogService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../../../config/devlogger.php' => config_path('devlogger.php'),
        ], 'devlogger-config');

        // Publish migrations
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../../migrations/' => database_path('migrations'),
            ], 'devlogger-migrations');

            // Register commands
            $this->commands([
                CleanupLogsCommand::class,
            ]);
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../../migrations');

        // Schedule cleanup if retention is configured
        if (config('devlogger.retention_days')) {
            $this->app->booted(function () {
                $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
                $schedule->command('devlogger:cleanup')->daily();
            });
        }
    }
}