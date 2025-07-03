<?php

namespace DevLoggerPackage\Console\Commands;

use DevLoggerPackage\Facades\DevLogger;
use Illuminate\Console\Command;

class CleanupLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'devlogger:cleanup 
                            {--days= : Number of days to keep logs (overrides config)}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old developer logs based on retention policy';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = $this->option('days') ?? config('devlogger.retention_days');
        $dryRun = $this->option('dry-run');

        if (!$days) {
            $this->error('No retention policy configured. Set DEVLOGGER_RETENTION_DAYS or use --days option.');
            return 1;
        }

        $this->info("Cleaning up logs older than {$days} days...");

        if ($dryRun) {
            $count = \DevLoggerPackage\Models\DeveloperLog::where('created_at', '<', now()->subDays($days))->count();
            $this->info("Would delete {$count} log entries (dry run).");
            return 0;
        }

        $deletedCount = DevLogger::cleanup();
        
        $this->info("Successfully deleted {$deletedCount} old log entries.");
        
        return 0;
    }
}