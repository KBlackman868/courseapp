<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\MoodleException;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseAccessRequest;
use App\Models\SystemNotification;
use App\Services\MoodleClient;
use App\Services\ActivityLogger;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * EnrollUserIntoMoodleCourse Job
 *
 * This job enrolls a user into a Moodle course after their access request
 * has been approved.
 *
 * WORKFLOW:
 * 1. Course Admin approves course access request
 * 2. This job is queued (possibly after CreateMoodleUserJob)
 * 3. Job enrolls user in Moodle course
 * 4. On success: Access request marked as synced, user notified
 * 5. On failure: Access request marked as failed, Course Admin notified
 *
 * IDEMPOTENCY:
 * - If user is already enrolled, the job succeeds without error
 * - Multiple runs won't create duplicate enrollments
 *
 * RETRY BEHAVIOR:
 * - Retries 3 times with exponential backoff
 * - On final failure, notifies Course Admin
 */
class EnrollUserIntoMoodleCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times to retry before failing
     */
    public int $tries = 3;

    /**
     * Maximum time in seconds the job can run
     */
    public int $timeout = 60;

    /**
     * Backoff between retries (in seconds)
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance
     *
     * @param User $user The user to enroll
     * @param Course $course The course to enroll them in
     * @param CourseAccessRequest|null $accessRequest The access request (for status updates)
     * @param int|null $roleId Moodle role ID (defaults to student role)
     */
    public function __construct(
        private User $user,
        private Course $course,
        private ?CourseAccessRequest $accessRequest = null,
        private ?int $roleId = null
    ) {}

    /**
     * Execute the job
     */
    public function handle(MoodleClient $moodleClient): void
    {
        Log::info('Starting Moodle course enrollment', [
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'moodle_course_id' => $this->course->moodle_course_id,
            'access_request_id' => $this->accessRequest?->id,
        ]);

        // Mark access request as syncing
        $this->accessRequest?->markSyncing();

        // Ensure course has Moodle integration
        if (!$this->course->moodle_course_id) {
            throw new MoodleException(
                "Course '{$this->course->title}' is not linked to Moodle"
            );
        }

        // Ensure user has a Moodle account
        if (!$this->user->moodle_user_id) {
            Log::info('User lacks Moodle ID, creating/linking account first', [
                'user_id' => $this->user->id,
            ]);

            // Dispatch synchronous job to create/link Moodle user
            CreateOrLinkMoodleUser::dispatchSync($this->user);

            // Refresh user to get the updated moodle_user_id
            $this->user->refresh();

            if (!$this->user->moodle_user_id) {
                throw new MoodleException(
                    'Failed to create/link Moodle user before enrollment'
                );
            }
        }

        // Perform the enrollment
        $this->enrollUser($moodleClient);

        // Mark as successfully synced
        $this->accessRequest?->markSynced();

        // Notify user that enrollment is ready
        if ($this->accessRequest) {
            SystemNotification::notifyEnrollmentReady($this->accessRequest);
        }

        // Log successful enrollment
        ActivityLogger::logMoodle(
            'enrollment_synced',
            "Enrolled user in Moodle course: {$this->course->title}",
            $this->user,
            [
                'course_id' => $this->course->id,
                'moodle_course_id' => $this->course->moodle_course_id,
                'moodle_user_id' => $this->user->moodle_user_id,
            ]
        );

        Log::info('Successfully completed Moodle course enrollment', [
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
        ]);
    }

    /**
     * Perform the actual enrollment in Moodle
     */
    private function enrollUser(MoodleClient $moodleClient): void
    {
        // Default role ID for student is 5 in Moodle
        $roleId = $this->roleId ?? config('moodle.default_student_role_id', 5);

        $enrollmentData = [
            'enrolments' => [
                [
                    'roleid' => $roleId,
                    'userid' => $this->user->moodle_user_id,
                    'courseid' => $this->course->moodle_course_id,
                ],
            ],
        ];

        try {
            $moodleClient->call('enrol_manual_enrol_users', $enrollmentData);

            Log::info('Successfully enrolled user in Moodle course', [
                'user_id' => $this->user->id,
                'moodle_user_id' => $this->user->moodle_user_id,
                'moodle_course_id' => $this->course->moodle_course_id,
                'role_id' => $roleId,
            ]);
        } catch (MoodleException $e) {
            // Check if it's a duplicate enrollment error (user already enrolled)
            // This is an idempotent operation - already enrolled is success
            $errorMessage = strtolower($e->getMessage());
            if (str_contains($errorMessage, 'already enrolled') ||
                str_contains($errorMessage, 'duplicate') ||
                str_contains($errorMessage, 'user is already enrolled')) {

                Log::warning('User already enrolled in course (idempotent success)', [
                    'user_id' => $this->user->id,
                    'moodle_course_id' => $this->course->moodle_course_id,
                ]);

                // This is still a success - user is enrolled
                return;
            }

            throw $e;
        }
    }

    /**
     * Handle job failure
     * Called after all retries have been exhausted
     */
    public function failed(\Throwable $exception): void
    {
        $errorMessage = $exception->getMessage();

        Log::error('Failed to enroll user in Moodle course after all retries', [
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'moodle_course_id' => $this->course->moodle_course_id,
            'access_request_id' => $this->accessRequest?->id,
            'error' => $errorMessage,
            'attempts' => $this->attempts(),
        ]);

        // Mark access request as failed
        if ($this->accessRequest) {
            $this->accessRequest->markSyncFailed($errorMessage);

            // Notify Course Admins about the failure
            SystemNotification::notifyMoodleSyncFailed($this->accessRequest);
        }

        // Update user's Moodle sync status
        $this->user->update([
            'moodle_sync_status' => 'failed',
            'moodle_sync_error' => $errorMessage,
        ]);

        // Log the failure for audit
        ActivityLogger::logMoodle(
            'enrollment_failed',
            "Failed to enroll user in Moodle course: {$this->course->title}",
            $this->user,
            [
                'course_id' => $this->course->id,
                'moodle_course_id' => $this->course->moodle_course_id,
                'error' => $errorMessage,
                'attempts' => $this->attempts(),
            ],
            'failed',
            'error'
        );
    }

    /**
     * Get the tags for the job (for monitoring)
     */
    public function tags(): array
    {
        return [
            'moodle',
            'enrollment',
            'user:' . $this->user->id,
            'course:' . $this->course->id,
        ];
    }
}
