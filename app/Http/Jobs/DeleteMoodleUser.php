<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MoodleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteMoodleUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    private int $moodleUserId;
    private int $localUserId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $moodleUserId, int $localUserId)
    {
        $this->moodleUserId = $moodleUserId;
        $this->localUserId = $localUserId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $moodleService = new MoodleService();
            
            // Create a temporary user object with just the Moodle ID
            $tempUser = new User();
            $tempUser->id = $this->localUserId;
            $tempUser->moodle_user_id = $this->moodleUserId;
            
            $result = $moodleService->deleteUser($tempUser);
            
            if ($result) {
                Log::info('Moodle user deleted via job', [
                    'local_user_id' => $this->localUserId,
                    'moodle_user_id' => $this->moodleUserId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete Moodle user in job', [
                'moodle_user_id' => $this->moodleUserId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job failed after all retries - Moodle user deletion', [
            'moodle_user_id' => $this->moodleUserId,
            'error' => $exception->getMessage()
        ]);
    }
}