<?php

namespace App\Http\Controllers;

use App\Jobs\CreateOrLinkMoodleUser;
use App\Jobs\EnrollUserIntoMoodleCourse;
use App\Mail\NewCourseEnrollmentEmail;
use App\Mail\EnrollmentConfirmationEmail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\NewCourseEnrollmentNotification;
use App\Services\EmailNotificationService;
use App\Services\MoodleClient;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    private ?MoodleClient $moodleClient = null;
    private ?EmailNotificationService $emailService = null;

    public function __construct()
    {
        // Make MoodleClient optional - don't fail if it's not configured
        try {
            $this->moodleClient = app(MoodleClient::class);
        } catch (\Exception $e) {
            $this->moodleClient = null;
            Log::info('Moodle client not configured, Moodle sync disabled');
        }

        // Make EmailNotificationService optional too
        try {
            $this->emailService = app(EmailNotificationService::class);
        } catch (\Exception $e) {
            $this->emailService = null;
            Log::warning('EmailNotificationService not available');
        }
    }

    /**
     * Store a new enrollment request
     */
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();

        // Check if the user is already enrolled in this course
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingEnrollment) {
            // Log duplicate attempt
            ActivityLogger::logEnrollment('duplicate_attempt', $existingEnrollment,
                "User attempted duplicate enrollment in course: {$course->title}",
                [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'existing_status' => $existingEnrollment->status,
                    'user_email' => $user->email
                ],
                'failed',
                'warning'
            );
            
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        // Create the enrollment record with status pending
        $enrollment = Enrollment::create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'status'    => 'pending',
        ]);

        // Log enrollment request
        ActivityLogger::logEnrollment('requested', $enrollment, 
            "User requested enrollment in course: {$course->title}",
            [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'user_email' => $user->email,
                'user_name' => "{$user->first_name} {$user->last_name}",
                'department' => $user->department
            ]
        );

        // Send enrollment confirmation email - Method 1: Using Service
        if ($this->emailService) {
            try {
                $this->emailService->sendEnrollmentConfirmation($enrollment);
                Log::info('Enrollment confirmation sent via EmailService', [
                    'enrollment_id' => $enrollment->id,
                    'user_email' => $user->email
                ]);
                
                // Log email sent
                ActivityLogger::logSystem('email_sent', 
                    "Enrollment confirmation email sent to {$user->email}",
                    ['enrollment_id' => $enrollment->id]
                );
            } catch (\Exception $e) {
                Log::error('Failed to send enrollment confirmation email via service', [
                    'error' => $e->getMessage(),
                    'enrollment_id' => $enrollment->id,
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Log email failure
                ActivityLogger::logSystem('email_failed',
                    "Failed to send enrollment confirmation to {$user->email}",
                    ['error' => $e->getMessage()],
                    'failed',
                    'error'
                );
                
                // Try direct mail as fallback
                try {
                    Mail::to($user->email)->send(new EnrollmentConfirmationEmail($enrollment));
                    Log::info('Enrollment confirmation sent directly via Mail facade', [
                        'enrollment_id' => $enrollment->id,
                        'user_email' => $user->email
                    ]);
                } catch (\Exception $fallbackError) {
                    Log::error('Direct mail also failed', [
                        'error' => $fallbackError->getMessage(),
                        'enrollment_id' => $enrollment->id
                    ]);
                }
            }
        } else {
            // Method 2: Direct Mail sending if service not available
            try {
                Mail::to($user->email)->send(new EnrollmentConfirmationEmail($enrollment));
                Log::info('Enrollment confirmation sent directly (no service)', [
                    'enrollment_id' => $enrollment->id,
                    'user_email' => $user->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send enrollment confirmation email directly', [
                    'error' => $e->getMessage(),
                    'enrollment_id' => $enrollment->id,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Send notification email to superadmins
        try {
            $superadmins = User::role('superadmin')->get();
            foreach ($superadmins as $admin) {
                Mail::to($admin->email)->send(new NewCourseEnrollmentEmail($enrollment));
            }
            Log::info('Admin notifications sent', [
                'enrollment_id' => $enrollment->id,
                'admin_count' => $superadmins->count()
            ]);
            
            // Log admin notification
            ActivityLogger::logSystem('admin_notification_sent',
                "Admin notifications sent for new enrollment",
                [
                    'enrollment_id' => $enrollment->id,
                    'admin_count' => $superadmins->count()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification email', [
                'error' => $e->getMessage(),
                'enrollment_id' => $enrollment->id
            ]);
        }

        // Add a flash message about the email
        $emailStatus = 'Your enrollment request has been submitted.';
        if (app()->environment('local')) {
            // In local environment, add more debugging info
            $emailStatus .= ' Check Laravel Telescope for email activity.';
        }

        return redirect()->route('home')->with('success', $emailStatus);
    }

    /**
     * Display list of enrollments for admin approval
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        
        $enrollments = Enrollment::where('status', $status)
                                ->with('user', 'course')
                                ->get();

        $users = User::all();
        
        // Log admin viewing enrollments
        ActivityLogger::logSystem('enrollment_list_viewed',
            "Admin viewed {$status} enrollments",
            [
                'status_filter' => $status,
                'count' => $enrollments->count(),
                'admin' => auth()->user()->email
            ]
        );
        
        return view('admin.approval_lists', compact('enrollments', 'status', 'users'));
    }

    /**
     * Update enrollment status (approve/deny)
     */
    public function update(Request $request, $enrollmentId)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,denied',
        ]);

        $enrollment = Enrollment::findOrFail($enrollmentId);
        $oldStatus = $enrollment->status;
        $enrollment->status = $request->status;
        $enrollment->save();

        // Log status changes
        if ($request->status === 'approved' && $oldStatus !== 'approved') {
            // Log approval
            ActivityLogger::logEnrollment('approved', $enrollment,
                "Enrollment approved for {$enrollment->user->email} in {$enrollment->course->title}",
                [
                    'approved_by' => auth()->user()->email,
                    'user_id' => $enrollment->user_id,
                    'course_id' => $enrollment->course_id,
                    'old_status' => $oldStatus,
                    'user_name' => "{$enrollment->user->first_name} {$enrollment->user->last_name}"
                ]
            );
            
            // Send approval email if status changed to approved
            if ($this->emailService) {
                try {
                    $this->emailService->sendEnrollmentApproved($enrollment);
                    
                    // Log email sent
                    ActivityLogger::logSystem('approval_email_sent',
                        "Approval email sent to {$enrollment->user->email}",
                        ['enrollment_id' => $enrollment->id]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send enrollment approved email', [
                        'error' => $e->getMessage(),
                        'enrollment_id' => $enrollment->id
                    ]);
                    
                    // Log email failure
                    ActivityLogger::logSystem('approval_email_failed',
                        "Failed to send approval email to {$enrollment->user->email}",
                        ['error' => $e->getMessage()],
                        'failed',
                        'error'
                    );
                }
            }
            
            // Sync to Moodle
            $this->syncApprovedEnrollmentToMoodle($enrollment);
        }
        
        if ($request->status === 'denied' && $oldStatus !== 'denied') {
            // Log denial
            ActivityLogger::logEnrollment('denied', $enrollment,
                "Enrollment denied for {$enrollment->user->email} in {$enrollment->course->title}",
                [
                    'denied_by' => auth()->user()->email,
                    'user_id' => $enrollment->user_id,
                    'course_id' => $enrollment->course_id,
                    'old_status' => $oldStatus,
                    'user_name' => "{$enrollment->user->first_name} {$enrollment->user->last_name}"
                ],
                'success',
                'warning'
            );
        }

        // If enrollment was previously approved but now denied/cancelled, remove from Moodle
        if ($oldStatus === 'approved' && $request->status !== 'approved') {
            $this->removeEnrollmentFromMoodle($enrollment);
        }

        return redirect()->back()->with('success', 'Enrollment status updated successfully.');
    }

    /**
     * View the list of courses when enrolled
     */
    public function myCourses()
    {
        $user = Auth::user();
        
        $enrollments = Enrollment::where('user_id', $user->id)
                                ->where('status', 'approved')
                                ->with('course')
                                ->get();

        // Log user viewing their courses
        ActivityLogger::logSystem('my_courses_viewed',
            "User viewed their enrolled courses",
            [
                'user_id' => $user->id,
                'course_count' => $enrollments->count()
            ]
        );

        return view('courses.mycourses', compact('enrollments'));
    }

    /**
     * Unenroll user from a course
     */
    public function unenroll($enrollmentId)
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        
        if ($enrollment->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'superadmin'])) {
            // Log unauthorized attempt
            ActivityLogger::logEnrollment('unenroll_blocked', $enrollment,
                "Unauthorized unenroll attempt",
                [
                    'attempted_by' => auth()->user()->email,
                    'enrollment_id' => $enrollmentId,
                    'enrollment_user' => $enrollment->user->email
                ],
                'failed',
                'warning'
            );
            
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $oldStatus = $enrollment->status;
        
        $enrollment->status = 'cancelled';
        $enrollment->save();

        // Log cancellation
        ActivityLogger::logEnrollment('cancelled', $enrollment,
            "Enrollment cancelled for {$enrollment->user->email} in {$enrollment->course->title}",
            [
                'cancelled_by' => auth()->user()->email,
                'old_status' => $oldStatus,
                'self_cancelled' => $enrollment->user_id === Auth::id(),
                'course_title' => $enrollment->course->title
            ]
        );

        if ($oldStatus === 'approved') {
            $this->removeEnrollmentFromMoodle($enrollment);
        }

        return redirect()->route('home')->with('success', 'Successfully removed from enrollment.');
    }

    /**
     * Sync approved enrollment to Moodle
     */
    private function syncApprovedEnrollmentToMoodle(Enrollment $enrollment): void
    {
        if (!$this->moodleClient) {
            Log::info('Moodle sync skipped - client not configured');
            return;
        }

        try {
            $course = $enrollment->course;
            $user = $enrollment->user;

            if (!$course->moodle_course_id) {
                Log::info('Course not synced to Moodle, skipping enrollment sync', [
                    'course_id' => $course->id,
                    'enrollment_id' => $enrollment->id,
                ]);
                return;
            }

            if (!$user->moodle_user_id) {
                Log::info('Creating Moodle user before enrollment', [
                    'user_id' => $user->id,
                ]);
                
                CreateOrLinkMoodleUser::dispatchSync($user);
                
                $user->refresh();
                
                if (!$user->moodle_user_id) {
                    Log::error('Failed to create Moodle user', [
                        'user_id' => $user->id,
                        'enrollment_id' => $enrollment->id,
                    ]);
                    
                    // Log Moodle sync failure
                    ActivityLogger::logMoodle('enrollment_sync_failed',
                        "Failed to create Moodle user for enrollment",
                        $enrollment,
                        ['user_id' => $user->id],
                        'failed',
                        'error'
                    );
                    return;
                }
            }

            EnrollUserIntoMoodleCourse::dispatch($user, $course->moodle_course_id);

            Log::info('Moodle enrollment sync dispatched', [
                'enrollment_id' => $enrollment->id,
                'user_id' => $user->id,
                'moodle_user_id' => $user->moodle_user_id,
                'course_id' => $course->id,
                'moodle_course_id' => $course->moodle_course_id,
            ]);
            
            // Log successful Moodle sync
            ActivityLogger::logMoodle('enrollment_synced',
                "Enrollment synced to Moodle for {$user->email}",
                $enrollment,
                [
                    'moodle_user_id' => $user->moodle_user_id,
                    'moodle_course_id' => $course->moodle_course_id
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to sync enrollment to Moodle', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);
            
            // Log Moodle sync error
            ActivityLogger::logMoodle('enrollment_sync_error',
                "Error syncing enrollment to Moodle",
                $enrollment,
                ['error' => $e->getMessage()],
                'failed',
                'error'
            );
        }
    }

    /**
     * Remove enrollment from Moodle when cancelled or denied
     */
    private function removeEnrollmentFromMoodle(Enrollment $enrollment): void
    {
        if (!$this->moodleClient) {
            return;
        }

        try {
            $course = $enrollment->course;
            $user = $enrollment->user;

            if (!$course->moodle_course_id || !$user->moodle_user_id) {
                return;
            }

            $unenrollData = [
                'enrolments' => [
                    [
                        'userid' => $user->moodle_user_id,
                        'courseid' => $course->moodle_course_id,
                    ],
                ],
            ];

            $this->moodleClient->call('enrol_manual_unenrol_users', $unenrollData);

            Log::info('User unenrolled from Moodle course', [
                'user_id' => $user->id,
                'moodle_user_id' => $user->moodle_user_id,
                'course_id' => $course->id,
                'moodle_course_id' => $course->moodle_course_id,
            ]);
            
            // Log Moodle unenrollment
            ActivityLogger::logMoodle('user_unenrolled',
                "User removed from Moodle course",
                $enrollment,
                [
                    'moodle_user_id' => $user->moodle_user_id,
                    'moodle_course_id' => $course->moodle_course_id
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to unenroll user from Moodle', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);
            
            // Log Moodle unenrollment error
            ActivityLogger::logMoodle('unenroll_error',
                "Failed to remove user from Moodle course",
                $enrollment,
                ['error' => $e->getMessage()],
                'failed',
                'error'
            );
        }
    }

    /**
     * Manually sync an enrollment to Moodle (admin action)
     */
    public function syncEnrollmentToMoodle(Request $request, $enrollmentId)
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        
        if ($enrollment->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved enrollments can be synced to Moodle.');
        }

        // Log manual sync attempt
        ActivityLogger::logMoodle('manual_sync_initiated',
            "Admin manually initiated Moodle sync for enrollment",
            $enrollment,
            ['admin' => auth()->user()->email]
        );

        $this->syncApprovedEnrollmentToMoodle($enrollment);

        return redirect()->back()->with('success', 'Enrollment sync to Moodle initiated.');
    }

    /**
     * Bulk sync all approved enrollments for a course to Moodle
     */
    public function bulkSyncCourseEnrollments(Request $request, Course $course)
    {
        if (!$course->moodle_course_id) {
            return redirect()->back()->with('error', 'Course is not synced to Moodle.');
        }

        $approvedEnrollments = Enrollment::where('course_id', $course->id)
            ->where('status', 'approved')
            ->with('user')
            ->get();

        $syncCount = 0;
        $failCount = 0;
        
        foreach ($approvedEnrollments as $enrollment) {
            if (!$enrollment->user->moodle_user_id) {
                CreateOrLinkMoodleUser::dispatchSync($enrollment->user);
                $enrollment->user->refresh();
            }
            
            if ($enrollment->user->moodle_user_id) {
                EnrollUserIntoMoodleCourse::dispatch(
                    $enrollment->user, 
                    $course->moodle_course_id
                );
                $syncCount++;
            } else {
                Log::error('Failed to create Moodle user for bulk sync', [
                    'user_id' => $enrollment->user->id
                ]);
                $failCount++;
            }
        }
        
        // Log bulk sync
        ActivityLogger::logMoodle('bulk_enrollment_sync',
            "Bulk sync enrollments to Moodle for course: {$course->title}",
            $course,
            [
                'total_enrollments' => $approvedEnrollments->count(),
                'synced' => $syncCount,
                'failed' => $failCount,
                'admin' => auth()->user()->email
            ]
        );

        return redirect()->back()->with('success', "Initiated Moodle sync for {$syncCount} enrollments.");
    }
}