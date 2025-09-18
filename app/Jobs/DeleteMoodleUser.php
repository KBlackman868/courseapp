<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\MoodleClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteMoodleUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int $moodleUserId,
        private int $laravelUserId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(MoodleClient $moodleClient): void
    {
        try {
            // Call Moodle API to delete the user
            $response = $moodleClient->call('core_user_delete_users', [
                'userids' => [$this->moodleUserId]
            ]);

            Log::info('Moodle user deleted successfully', [
                'moodle_user_id' => $this->moodleUserId,
                'laravel_user_id' => $this->laravelUserId
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete Moodle user', [
                'moodle_user_id' => $this->moodleUserId,
                'laravel_user_id' => $this->laravelUserId,
                'error' => $e->getMessage()
            ]);
            
            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Permanently failed to delete Moodle user after retries', [
            'moodle_user_id' => $this->moodleUserId,
            'laravel_user_id' => $this->laravelUserId,
            'error' => $exception->getMessage()
        ]);
    }
}