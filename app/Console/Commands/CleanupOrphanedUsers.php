<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Enrollment;
use App\Jobs\DeleteMoodleUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CleanupOrphanedUsers extends Command
{
    protected $signature = 'users:cleanup-orphaned
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--days=30 : Only clean records deleted more than N days ago}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Clean up orphaned user records and their associated data';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $days = (int) $this->option('days');

        $this->info($dryRun ? '=== DRY RUN MODE ===' : '=== CLEANUP MODE ===');
        $this->info("Looking for orphaned records older than {$days} days...");
        $this->newLine();

        $cutoff = Carbon::now()->subDays($days);

        // 1. Find soft-deleted users (if using SoftDeletes) or suspended users deleted long ago
        $orphanedUsers = $this->findOrphanedUsers($cutoff);

        // 2. Find enrollments without valid users
        $orphanedEnrollments = Enrollment::whereNotIn('user_id', User::pluck('id'))->get();

        // 3. Find enrollments for courses that no longer exist
        $orphanedCourseEnrollments = DB::table('enrollments')
            ->leftJoin('courses', 'enrollments.course_id', '=', 'courses.id')
            ->whereNull('courses.id')
            ->select('enrollments.*')
            ->get();

        // Summary
        $this->info('Found:');
        $this->line("  - {$orphanedUsers->count()} orphaned/suspended users (inactive > {$days} days)");
        $this->line("  - {$orphanedEnrollments->count()} enrollments with missing users");
        $this->line("  - {$orphanedCourseEnrollments->count()} enrollments with missing courses");
        $this->newLine();

        $totalItems = $orphanedUsers->count() + $orphanedEnrollments->count() + $orphanedCourseEnrollments->count();

        if ($totalItems === 0) {
            $this->info('No orphaned records found. Database is clean!');
            return 0;
        }

        // Show details
        if ($orphanedUsers->isNotEmpty()) {
            $this->table(
                ['ID', 'Name', 'Email', 'Suspended', 'Last Activity'],
                $orphanedUsers->map(fn ($u) => [
                    $u->id,
                    "{$u->first_name} {$u->last_name}",
                    $u->email,
                    $u->is_suspended ? 'Yes' : 'No',
                    $u->updated_at?->diffForHumans() ?? 'N/A',
                ])->toArray()
            );
        }

        if ($dryRun) {
            $this->warn('DRY RUN: No records were deleted.');
            $this->info('Run without --dry-run to perform the actual cleanup.');
            return 0;
        }

        // Confirm unless --force
        if (!$this->option('force') && !$this->confirm("Proceed with deleting {$totalItems} orphaned records?")) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        // Execute cleanup
        $stats = $this->executeCleanup($orphanedUsers, $orphanedEnrollments, $orphanedCourseEnrollments);

        // Log and display results
        $this->newLine();
        $this->info('=== Cleanup Complete ===');
        $this->line("  Users deleted: {$stats['users_deleted']}");
        $this->line("  Moodle deletions queued: {$stats['moodle_queued']}");
        $this->line("  Orphaned enrollments removed: {$stats['enrollments_deleted']}");
        $this->line("  Failed: {$stats['failed']}");

        Log::info('Orphaned user cleanup completed', $stats);

        // Send summary email
        $this->sendSummaryEmail($stats);

        return 0;
    }

    private function findOrphanedUsers(Carbon $cutoff)
    {
        return User::where(function ($query) use ($cutoff) {
            // Suspended users who haven't been active since cutoff
            $query->where('is_suspended', true)
                  ->where('updated_at', '<', $cutoff);
        })->orWhere(function ($query) use ($cutoff) {
            // Users with no roles, no enrollments, not logged in recently
            $query->whereDoesntHave('roles')
                  ->whereDoesntHave('enrollments')
                  ->where('created_at', '<', $cutoff)
                  ->whereNull('email_verified_at');
        })->get();
    }

    private function executeCleanup($orphanedUsers, $orphanedEnrollments, $orphanedCourseEnrollments): array
    {
        $stats = [
            'users_deleted' => 0,
            'moodle_queued' => 0,
            'enrollments_deleted' => 0,
            'failed' => 0,
        ];

        DB::beginTransaction();

        try {
            // Delete orphaned enrollments (no valid user)
            if ($orphanedEnrollments->isNotEmpty()) {
                $count = Enrollment::whereIn('id', $orphanedEnrollments->pluck('id'))->delete();
                $stats['enrollments_deleted'] += $count;
                $this->line("Deleted {$count} enrollments with missing users.");
            }

            // Delete enrollments with missing courses
            if ($orphanedCourseEnrollments->isNotEmpty()) {
                $ids = $orphanedCourseEnrollments->pluck('id')->toArray();
                $count = DB::table('enrollments')->whereIn('id', $ids)->delete();
                $stats['enrollments_deleted'] += $count;
                $this->line("Deleted {$count} enrollments with missing courses.");
            }

            // Delete orphaned users
            foreach ($orphanedUsers as $user) {
                try {
                    $moodleUserId = $user->moodle_user_id;
                    $userId = $user->id;

                    // Remove their enrollments first
                    Enrollment::where('user_id', $userId)->delete();

                    // Delete the user
                    $user->delete();
                    $stats['users_deleted']++;

                    // Queue Moodle deletion
                    if ($moodleUserId) {
                        DeleteMoodleUser::dispatch($moodleUserId, $userId);
                        $stats['moodle_queued']++;
                    }

                    $this->line("Deleted user: {$user->email} (ID: {$userId})");
                } catch (\Exception $e) {
                    $stats['failed']++;
                    $this->error("Failed to delete user {$user->email}: {$e->getMessage()}");
                    Log::error('Orphaned user cleanup failed for user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Cleanup failed: {$e->getMessage()}");
            Log::error('Orphaned user cleanup transaction failed', ['error' => $e->getMessage()]);
            $stats['failed'] = $orphanedUsers->count();
        }

        return $stats;
    }

    private function sendSummaryEmail(array $stats): void
    {
        $totalCleaned = $stats['users_deleted'] + $stats['enrollments_deleted'];

        if ($totalCleaned === 0) {
            return;
        }

        try {
            Mail::raw(
                "Weekly Orphaned Records Cleanup Summary\n" .
                "========================================\n\n" .
                "Users deleted: {$stats['users_deleted']}\n" .
                "Moodle deletions queued: {$stats['moodle_queued']}\n" .
                "Orphaned enrollments removed: {$stats['enrollments_deleted']}\n" .
                "Failed operations: {$stats['failed']}\n\n" .
                "This is an automated cleanup report from the MOH Learning Platform.",
                function ($message) {
                    $message->to('helpdesk@health.gov.tt')
                            ->subject('MOH Learning - Weekly Cleanup Report');
                }
            );
            $this->info('Summary email sent to helpdesk@health.gov.tt');
        } catch (\Exception $e) {
            $this->warn("Failed to send summary email: {$e->getMessage()}");
        }
    }
}
