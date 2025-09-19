<?php
namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Mail\EnrollmentConfirmationEmail;
use App\Mail\EnrollmentApprovedEmail;
use App\Mail\CourseReminderEmail;
use App\Mail\MoodleSyncSuccessEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    /**
     * Send enrollment confirmation email
     */
    public function sendEnrollmentConfirmation(Enrollment $enrollment): void
    {
        try {
            Mail::to($enrollment->user->email)
                ->send(new EnrollmentConfirmationEmail($enrollment));
                
            Log::info('Enrollment confirmation email sent', [
                'user_id' => $enrollment->user_id,
                'course_id' => $enrollment->course_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send enrollment confirmation email', [
                'error' => $e->getMessage(),
                'enrollment_id' => $enrollment->id,
            ]);
        }
    }
    
    /**
     * Send enrollment approved email
     */
    public function sendEnrollmentApproved(Enrollment $enrollment): void
    {
        try {
            Mail::to($enrollment->user->email)
                ->send(new EnrollmentApprovedEmail($enrollment));
                
            Log::info('Enrollment approved email sent', [
                'user_id' => $enrollment->user_id,
                'course_id' => $enrollment->course_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send enrollment approved email', [
                'error' => $e->getMessage(),
                'enrollment_id' => $enrollment->id,
            ]);
        }
    }
    
    /**
     * Send Moodle sync success email
     */
    public function sendMoodleSyncSuccess(User $user): void
    {
        try {
            Mail::to($user->email)
                ->send(new MoodleSyncSuccessEmail($user));
                
            Log::info('Moodle sync success email sent', [
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send Moodle sync email', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
        }
    }
}