<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\MoodleException;
use App\Models\User;
use App\Services\MoodleClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnrollUserIntoMoodleCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        private User $user,
        private int $moodleCourseId,
        private ?int $roleId = null
    ) {}

    public function handle(MoodleClient $moodleClient): void
    {
        // Ensure user has a Moodle account
        if (!$this->user->moodle_user_id) {
            Log::info('User lacks Moodle ID, creating/linking account first', [
                'user_id' => $this->user->id,
            ]);
            
            // Dispatch job to create/link Moodle user first
            CreateOrLinkMoodleUser::dispatchSync($this->user);
            
            // Refresh user to get the updated moodle_user_id
            $this->user->refresh();
            
            if (!$this->user->moodle_user_id) {
                throw new MoodleException('Failed to create/link Moodle user before enrollment');
            }
        }

        // Perform the enrollment
        $this->enrollUser($moodleClient);
    }

    private function enrollUser(MoodleClient $moodleClient): void
    {
        $roleId = $this->roleId ?? config('moodle.default_student_role_id');
        
        $enrollmentData = [
            'enrolments' => [
                [
                    'roleid' => $roleId,
                    'userid' => $this->user->moodle_user_id,
                    'courseid' => $this->moodleCourseId,
                ],
            ],
        ];

        try {
            $moodleClient->call('enrol_manual_enrol_users', $enrollmentData);
            
            Log::info('Successfully enrolled user in Moodle course', [
                'user_id' => $this->user->id,
                'moodle_user_id' => $this->user->moodle_user_id,
                'moodle_course_id' => $this->moodleCourseId,
                'role_id' => $roleId,
            ]);
        } catch (MoodleException $e) {
            // Check if it's a duplicate enrollment error (user already enrolled)
            if (str_contains($e->getMessage(), 'already enrolled')) {
                Log::warning('User already enrolled in course', [
                    'user_id' => $this->user->id,
                    'moodle_course_id' => $this->moodleCourseId,
                ]);
                return; // Idempotent - don't throw error if already enrolled
            }
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to enroll user in Moodle course', [
            'user_id' => $this->user->id,
            'moodle_course_id' => $this->moodleCourseId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}