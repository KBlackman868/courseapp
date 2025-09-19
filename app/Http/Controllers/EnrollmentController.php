<?php

namespace App\Http\Controllers;

use App\Jobs\CreateOrLinkMoodleUser;
use App\Jobs\EnrollUserIntoMoodleCourse;
use App\Mail\NewCourseEnrollmentEmail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\NewCourseEnrollmentNotification;
use App\Services\MoodleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    private ?MoodleClient $moodleClient = null;
    private EmailNotificationService $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
        // Make MoodleClient optional - don't fail if it's not configured
        try {
            $this->moodleClient = app(MoodleClient::class);
        } catch (\Exception $e) {
            // Moodle not configured, continue without it
            $this->moodleClient = null;
            Log::info('Moodle client not configured, Moodle sync disabled');
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
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        // Create the enrollment record with status pending
        $enrollment = Enrollment::create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'status'    => 'pending',
        ]);

        $this->emailService->sendEnrollmentConfirmation($enrollment);
        
        // Send notification email to superadmins
        $superadmins = User::role('superadmin')->get();
        foreach ($superadmins as $admin) {
            Mail::to($admin->email)->send(new NewCourseEnrollmentEmail($enrollment));
        }

        // Note: We don't sync to Moodle yet since enrollment is pending
        // Moodle sync happens when admin approves the enrollment

        return redirect()->route('home')->with('success', 'Your enrollment request has been submitted.');
    }

    /**
     * Display list of enrollments for admin approval
     */
    public function index(Request $request)
    {
        // Get the 'status' query parameter, defaulting to 'pending'
        $status = $request->query('status', 'pending');
        
        // Retrieve enrollments with the given status along with user and course data
        $enrollments = Enrollment::where('status', $status)
                                ->with('user', 'course')
                                ->get();

        // Retrieve all users
        $users = User::all();
        
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

        // If enrollment is approved and course has Moodle integration, sync to Moodle
        if ($request->status === 'approved' && $oldStatus !== 'approved') {
            $this->syncApprovedEnrollmentToMoodle($enrollment);
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
        
        // Retrieve enrollments for the current user where status is approved
        $enrollments = Enrollment::where('user_id', $user->id)
                                ->where('status', 'approved')
                                ->with('course')
                                ->get();

        return view('courses.mycourses', compact('enrollments'));
    }

    /**
     * Unenroll user from a course
     */
    public function unenroll($enrollmentId)
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        
        // Check if user is authorized to unenroll (either the enrolled user or admin)
        if ($enrollment->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $oldStatus = $enrollment->status;
        
        // Update status to "cancelled"
        $enrollment->status = 'cancelled';
        $enrollment->save();

        // If enrollment was approved and course has Moodle integration, remove from Moodle
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
        // Skip if Moodle client is not configured
        if (!$this->moodleClient) {
            Log::info('Moodle sync skipped - client not configured');
            return;
        }

        try {
            $course = $enrollment->course;
            $user = $enrollment->user;

            // Only sync if course has Moodle integration
            if (!$course->moodle_course_id) {
                Log::info('Course not synced to Moodle, skipping enrollment sync', [
                    'course_id' => $course->id,
                    'enrollment_id' => $enrollment->id,
                ]);
                return;
            }

            // First ensure user exists in Moodle
            if (!$user->moodle_user_id) {
                Log::info('Creating Moodle user before enrollment', [
                    'user_id' => $user->id,
                ]);
                
                // Create user synchronously to ensure it completes
                CreateOrLinkMoodleUser::dispatchSync($user);
                
                // Refresh user to get the updated moodle_user_id
                $user->refresh();
                
                // Check if user was successfully created
                if (!$user->moodle_user_id) {
                    Log::error('Failed to create Moodle user', [
                        'user_id' => $user->id,
                        'enrollment_id' => $enrollment->id,
                    ]);
                    return;
                }
            }

            // Now enroll the user (user definitely exists in Moodle at this point)
            EnrollUserIntoMoodleCourse::dispatch($user, $course->moodle_course_id);

            Log::info('Moodle enrollment sync dispatched', [
                'enrollment_id' => $enrollment->id,
                'user_id' => $user->id,
                'moodle_user_id' => $user->moodle_user_id,
                'course_id' => $course->id,
                'moodle_course_id' => $course->moodle_course_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to sync enrollment to Moodle', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - we don't want to break the enrollment process
            // Consider notifying admin about the sync failure
        }
    }

    /**
     * Remove enrollment from Moodle when cancelled or denied
     */
    private function removeEnrollmentFromMoodle(Enrollment $enrollment): void
    {
        // Skip if Moodle client is not configured
        if (!$this->moodleClient) {
            return;
        }

        try {
            $course = $enrollment->course;
            $user = $enrollment->user;

            // Only proceed if both user and course have Moodle IDs
            if (!$course->moodle_course_id || !$user->moodle_user_id) {
                return;
            }

            // Call Moodle API to unenroll user
            // Note: This requires the 'enrol_manual_unenrol_users' function in Moodle
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

        } catch (\Exception $e) {
            Log::error('Failed to unenroll user from Moodle', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - unenrollment in local system should still succeed
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
        foreach ($approvedEnrollments as $enrollment) {
            if (!$enrollment->user->moodle_user_id) {
                // Create user in Moodle first (synchronously)
                CreateOrLinkMoodleUser::dispatchSync($enrollment->user);
                
                // Refresh to get moodle_user_id
                $enrollment->user->refresh();
            }
            
            // Only enroll if user was successfully created/exists
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
            }
        }

        return redirect()->back()->with('success', "Initiated Moodle sync for {$syncCount} enrollments.");
    }
}