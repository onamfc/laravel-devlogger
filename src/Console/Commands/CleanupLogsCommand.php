<?php

namespace DevLoggerPackage\Console\Commands;

use DevLoggerPackage\Facades\DevLogger;
use DevLoggerPackage\Services\DeveloperLogService;
use Illuminate\Console\Command;

class CleanupLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'devlogger:cleanup
                            {--days= : Number of days to keep logs (overrides config)}
                            {--chunk= : Rows to remove per statement (default 1000)}
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
        $days = $this->option('days') ? (int) $this->option('days') : config('devlogger.retention_days');
        $chunk = $this->option('chunk') ? (int) $this->option('chunk') : DeveloperLogService::CLEANUP_CHUNK_SIZE;
        $dryRun = $this->option('dry-run');

        if (!$days) {
            $this->error('No retention policy configured. Set DEVLOGGER_RETENTION_DAYS or use --days option.');
            return 1;
        }

        $this->info("Cleaning up logs older than {$days} days...");

        if ($dryRun) {
            $count = DevLogger::cleanupCandidates($days);
            $this->info("Would delete {$count} log entries (dry run).");
            return 0;
        }

        /**
         * $days is handed on rather than dropped. This used to call cleanup() with no arguments,
         * and cleanup() read the config for itself - so --days changed the message printed above
         * and the dry-run count, then had no effect whatsoever on what was deleted.
         */
        $deletedCount = DevLogger::cleanup($days, $chunk);

        $this->info("Successfully deleted {$deletedCount} old log entries.");

        return 0;
    }
}