<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;
use Carbon\Carbon;

class CleanupActivityLogs extends Command
{
    protected $signature = 'logs:cleanup {--days=30 : Number of days to keep}';
    protected $description = 'Clean up old activity logs';

    public function handle()
    {
        $days = $this->option('days');
        $date = Carbon::now()->subDays($days);
        
        $count = ActivityLog::where('created_at', '<', $date)->count();
        
        if ($count > 0) {
            if ($this->confirm("This will delete {$count} logs older than {$days} days. Continue?")) {
                ActivityLog::where('created_at', '<', $date)->delete();
                $this->info("Deleted {$count} old activity logs.");
            }
        } else {
            $this->info("No logs older than {$days} days found.");
        }
        
        return 0;
    }
}