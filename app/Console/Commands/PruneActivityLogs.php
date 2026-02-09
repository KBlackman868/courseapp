<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PruneActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'activity-logs:prune {--days=90 : Number of days to retain logs}';

    /**
     * The console command description.
     */
    protected $description = 'Delete activity logs older than the specified retention period';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $count = ActivityLog::where('created_at', '<', $cutoff)->count();

        if ($count === 0) {
            $this->info("No activity logs older than {$days} days found.");
            return self::SUCCESS;
        }

        $this->info("Deleting {$count} activity log(s) older than {$days} days (before {$cutoff->toDateString()})...");

        ActivityLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Successfully deleted {$count} activity log(s).");

        return self::SUCCESS;
    }
}
