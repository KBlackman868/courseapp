<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Jobs\EnrollUserIntoMoodleCourse;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentRequest;
use App\Services\ActivityLogger;
use App\Services\MoodleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnrollmentRequestController extends Controller
{
    private ?MoodleClient $moodleClient = null;

    public function __construct()
    {
        try {
            $this->moodleClient = app(MoodleClient::class);
        } catch (\Exception $e) {
            $this->moodleClient = null;
            Log::info('Moodle client not configured');
        }
    }

    /**
     * User: Submit an enrollment request for a course
     */
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();

        // Check if user can view this course
        if (!$course->isVisibleTo($user)) {
            return back()->with('error', 'You do not have permission to request access to this course.');
        }

        // Check for existing request
        $existingRequest = EnrollmentRequest::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingRequest) {
            if ($existingRequest->isPending()) {
                return back()->with('error', 'You already have a pending request for this course.');
            }
            if ($existingRequest->isApproved()) {
                return back()->with('error', 'Your request has already been approved.');
            }
            // If denied, allow resubmission by updating existing request
            $existingRequest->update([
                'status' => EnrollmentRequest::STATUS_PENDING,
                'request_reason' => $request->input('reason'),
                'admin_notes' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);

            ActivityLogger::logEnrollment('request_resubmitted', $existingRequest,
                "User resubmitted enrollment request for: {$course->title}",
                [
                    'course_id' => $course->id,
                    'user_email' => $user->email,
                ]
            );

            return back()->with('success', 'Your enrollment request has been resubmitted.');
        }

        // Check for existing enrollment
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingEnrollment) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        // Create new request
        $enrollmentRequest = EnrollmentRequest::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => EnrollmentRequest::STATUS_PENDING,
            'request_reason' => $request->input('reason'),
        ]);

        ActivityLogger::logEnrollment('request_created', $enrollmentRequest,
            "User requested enrollment in: {$course->title}",
            [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'user_email' => $user->email,
                'reason' => $request->input('reason'),
            ]
        );

        return back()->with('success', 'Your enrollment request has been submitted. You will be notified when it is reviewed.');
    }

    /**
     * Admin: Display enrollment requests
     */
    public function adminIndex(Request $request)
    {
        $status = $request->get('status', 'pending');
        $courseId = $request->get('course_id');
        $userType = $request->get('user_type');

        $query = EnrollmentRequest::with(['user', 'course', 'reviewer'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($userType) {
            $query->whereHas('user', function ($q) use ($userType) {
                $q->where('user_type', $userType);
            });
        }

        $requests = $query->paginate(20);

        // Get counts for tabs
        $counts = [
            'pending' => EnrollmentRequest::pending()->count(),
            'approved' => EnrollmentRequest::approved()->count(),
            'denied' => EnrollmentRequest::denied()->count(),
        ];

        // Get courses for filter dropdown
        $courses = Course::orderBy('title')->get(['id', 'title']);

        ActivityLogger::logSystem('enrollment_requests_viewed',
            "Admin viewed enrollment requests",
            [
                'status_filter' => $status,
                'count' => $requests->total(),
                'admin' => auth()->user()->email,
            ]
        );

        return view('admin.enrollment-requests.index', compact('requests', 'status', 'counts', 'courses', 'courseId', 'userType'));
    }

    /**
     * Admin: Approve an enrollment request
     */
    public function approve(Request $request, EnrollmentRequest $enrollmentRequest)
    {
        if (!$enrollmentRequest->isPending()) {
            return back()->with('error', 'This request has already been processed.');
        }

        try {
            DB::transaction(function () use ($request, $enrollmentRequest) {
                // Update request status
                $enrollmentRequest->approve(
                    auth()->id(),
                    $request->input('admin_notes')
                );

                // Create enrollment record
                $enrollment = Enrollment::create([
                    'user_id' => $enrollmentRequest->user_id,
                    'course_id' => $enrollmentRequest->course_id,
                    'status' => 'approved',
                ]);

                // Sync to Moodle
                $this->syncEnrollmentToMoodle($enrollmentRequest);

                // Log approval
                ActivityLogger::logEnrollment('request_approved', $enrollmentRequest,
                    "Enrollment request approved for {$enrollmentRequest->user->email} in {$enrollmentRequest->course->title}",
                    [
                        'approved_by' => auth()->user()->email,
                        'course_id' => $enrollmentRequest->course_id,
                        'user_id' => $enrollmentRequest->user_id,
                        'admin_notes' => $request->input('admin_notes'),
                    ]
                );
            });

            // TODO: Send notification to user
            // $enrollmentRequest->user->notify(new EnrollmentApprovedNotification($enrollmentRequest->course));

            return back()->with('success', "Enrollment approved for {$enrollmentRequest->user->full_name}.");

        } catch (\Exception $e) {
            Log::error('Failed to approve enrollment request', [
                'request_id' => $enrollmentRequest->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve enrollment. Please try again.');
        }
    }

    /**
     * Admin: Deny an enrollment request
     */
    public function deny(Request $request, EnrollmentRequest $enrollmentRequest)
    {
        if (!$enrollmentRequest->isPending()) {
            return back()->with('error', 'This request has already been processed.');
        }

        try {
            $enrollmentRequest->deny(
                auth()->id(),
                $request->input('admin_notes')
            );

            ActivityLogger::logEnrollment('request_denied', $enrollmentRequest,
                "Enrollment request denied for {$enrollmentRequest->user->email} in {$enrollmentRequest->course->title}",
                [
                    'denied_by' => auth()->user()->email,
                    'course_id' => $enrollmentRequest->course_id,
                    'user_id' => $enrollmentRequest->user_id,
                    'admin_notes' => $request->input('admin_notes'),
                ],
                'success',
                'warning'
            );

            // TODO: Send notification to user
            // $enrollmentRequest->user->notify(new EnrollmentDeniedNotification($enrollmentRequest->course, $request->input('admin_notes')));

            return back()->with('success', "Enrollment denied for {$enrollmentRequest->user->full_name}.");

        } catch (\Exception $e) {
            Log::error('Failed to deny enrollment request', [
                'request_id' => $enrollmentRequest->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to deny enrollment. Please try again.');
        }
    }

    /**
     * Sync approved enrollment to Moodle
     */
    private function syncEnrollmentToMoodle(EnrollmentRequest $request): void
    {
        if (!$this->moodleClient) {
            Log::info('Moodle sync skipped - client not configured');
            return;
        }

        $course = $request->course;
        $user = $request->user;

        if (!$course->moodle_course_id) {
            Log::info('Course not synced to Moodle', ['course_id' => $course->id]);
            return;
        }

        try {
            // Ensure user has Moodle account
            if (!$user->moodle_user_id) {
                CreateOrLinkMoodleUser::dispatchSync($user);
                $user->refresh();
            }

            if ($user->moodle_user_id) {
                EnrollUserIntoMoodleCourse::dispatch($user, $course);

                ActivityLogger::logMoodle('enrollment_synced',
                    "Enrollment synced to Moodle",
                    $request,
                    [
                        'moodle_user_id' => $user->moodle_user_id,
                        'moodle_course_id' => $course->moodle_course_id,
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync enrollment to Moodle', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
