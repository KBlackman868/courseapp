<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseAccessRequest;
use App\Models\Course;
use App\Models\User;
use App\Models\SystemNotification;
use App\Services\ActivityLogger;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Jobs\EnrollUserIntoMoodleCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CourseAccessRequestController
 *
 * Handles the approval workflow for course access requests.
 *
 * WORKFLOW:
 * 1. User requests access to an APPROVAL_REQUIRED course
 * 2. Request created in "pending" status
 * 3. Course Admin reviews and approves/rejects
 * 4. On approval:
 *    - Moodle account created (if needed) via CreateMoodleUserJob
 *    - User enrolled in Moodle course via EnrollMoodleUserJob
 *    - User notified they can now access the course
 */
class CourseAccessRequestController extends Controller
{
    /**
     * Display the list of course access requests for admin
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CourseAccessRequest::class);

        // Get filter parameters
        $status = $request->input('status', 'pending');
        $courseId = $request->input('course_id');
        $search = $request->input('search');

        // Build query
        $query = CourseAccessRequest::query()
            ->with(['user', 'course', 'approver'])
            ->orderBy('requested_at', 'desc');

        // Apply filters
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(20);

        // Get courses for filter dropdown
        $courses = Course::orderBy('title')->pluck('title', 'id');

        // Get counts for tabs
        $counts = [
            'pending' => CourseAccessRequest::pending()->count(),
            'approved' => CourseAccessRequest::where('status', 'approved')->count(),
            'rejected' => CourseAccessRequest::where('status', 'rejected')->count(),
            'failed' => CourseAccessRequest::syncFailed()->count(),
            'all' => CourseAccessRequest::count(),
        ];

        return view('admin.course-access-requests.index', compact(
            'requests', 'courses', 'counts', 'status', 'courseId', 'search'
        ));
    }

    /**
     * Show a specific course access request
     */
    public function show(CourseAccessRequest $courseAccessRequest)
    {
        $this->authorize('view', $courseAccessRequest);

        return view('admin.course-access-requests.show', [
            'request' => $courseAccessRequest->load(['user', 'course', 'approver']),
        ]);
    }

    /**
     * Approve a course access request
     * Triggers Moodle sync jobs
     */
    public function approve(Request $request, CourseAccessRequest $courseAccessRequest)
    {
        $this->authorize('approve', $courseAccessRequest);

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $user = $courseAccessRequest->user;
            $course = $courseAccessRequest->course;

            // Approve the request
            $courseAccessRequest->approve(
                auth()->user(),
                $validated['admin_notes'] ?? null
            );

            // Queue Moodle account creation (if needed) and enrollment
            if ($course->hasMoodleIntegration()) {
                // Mark as syncing BEFORE dispatching, because with the sync queue
                // driver the job runs immediately and calls markSynced() on completion.
                // If markSyncing() runs after dispatch(), it overwrites 'synced' with 'syncing'.
                $courseAccessRequest->markSyncing();

                // First, ensure user has a Moodle account
                if (!$user->hasMoodleAccount()) {
                    CreateOrLinkMoodleUser::dispatch($user)->chain([
                        new EnrollUserIntoMoodleCourse($user, $course, $courseAccessRequest),
                    ]);
                } else {
                    // User already has Moodle account, just enroll
                    EnrollUserIntoMoodleCourse::dispatch($user, $course, $courseAccessRequest);
                }
            } else {
                // No Moodle integration, mark as synced immediately
                $courseAccessRequest->markSynced();
            }

            // Send notification to user
            SystemNotification::notifyCourseApproved($courseAccessRequest);

            // Log the action
            ActivityLogger::log(
                'course_access_approved',
                "Approved course access for {$user->full_name} to '{$course->title}'",
                $courseAccessRequest,
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                'success',
                'info'
            );

            DB::commit();

            return redirect()
                ->route('admin.course-access-requests.index')
                ->with('success', "Access approved for {$user->full_name} to '{$course->title}'.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve course access request', [
                'request_id' => $courseAccessRequest->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve request. Please try again.');
        }
    }

    /**
     * Reject a course access request
     */
    public function reject(Request $request, CourseAccessRequest $courseAccessRequest)
    {
        $this->authorize('reject', $courseAccessRequest);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $user = $courseAccessRequest->user;
        $course = $courseAccessRequest->course;

        $courseAccessRequest->reject(
            auth()->user(),
            $validated['rejection_reason'],
            $validated['admin_notes'] ?? null
        );

        // Send notification to user
        SystemNotification::notifyCourseRejected($courseAccessRequest);

        // Log the action
        ActivityLogger::log(
            'course_access_rejected',
            "Rejected course access for {$user->full_name} to '{$course->title}'",
            $courseAccessRequest,
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'rejection_reason' => $validated['rejection_reason'],
            ],
            'success',
            'warning'
        );

        return redirect()
            ->route('admin.course-access-requests.index')
            ->with('success', "Access rejected for {$user->full_name}.");
    }

    /**
     * Revoke previously granted access
     */
    public function revoke(Request $request, CourseAccessRequest $courseAccessRequest)
    {
        $this->authorize('revoke', $courseAccessRequest);

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $user = $courseAccessRequest->user;
        $course = $courseAccessRequest->course;

        $courseAccessRequest->revoke(auth()->user(), $validated['reason']);

        // Log the action
        ActivityLogger::log(
            'course_access_revoked',
            "Revoked course access for {$user->full_name} to '{$course->title}'",
            $courseAccessRequest,
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'reason' => $validated['reason'],
            ],
            'success',
            'warning'
        );

        return redirect()
            ->route('admin.course-access-requests.index')
            ->with('success', "Access revoked for {$user->full_name}.");
    }

    /**
     * Bulk approve course access requests
     */
    public function bulkApprove(Request $request)
    {
        $this->authorize('bulkApprove', CourseAccessRequest::class);

        $validated = $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:course_access_requests,id',
        ]);

        $successCount = 0;
        $failedCount = 0;

        DB::beginTransaction();

        try {
            foreach ($validated['request_ids'] as $requestId) {
                $accessRequest = CourseAccessRequest::find($requestId);

                if ($accessRequest && $accessRequest->isPending()) {
                    try {
                        $user = $accessRequest->user;
                        $course = $accessRequest->course;

                        $accessRequest->approve(auth()->user(), 'Bulk approved');

                        if ($course->hasMoodleIntegration()) {
                            $accessRequest->markSyncing();
                            if (!$user->hasMoodleAccount()) {
                                CreateOrLinkMoodleUser::dispatch($user)->chain([
                                    new EnrollUserIntoMoodleCourse($user, $course, $accessRequest),
                                ]);
                            } else {
                                EnrollUserIntoMoodleCourse::dispatch($user, $course, $accessRequest);
                            }
                        } else {
                            $accessRequest->markSynced();
                        }

                        SystemNotification::notifyCourseApproved($accessRequest);
                        $successCount++;
                    } catch (\Exception $e) {
                        $failedCount++;
                        Log::error('Failed to approve course access in bulk', [
                            'request_id' => $requestId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Log bulk action
            ActivityLogger::log(
                'bulk_course_access_approval',
                "Bulk approved {$successCount} course access requests",
                null,
                [
                    'approved_count' => $successCount,
                    'failed_count' => $failedCount,
                ],
                'success',
                'info'
            );

            DB::commit();

            $message = "Approved {$successCount} request(s).";
            if ($failedCount > 0) {
                $message .= " {$failedCount} failed.";
            }

            return redirect()
                ->route('admin.course-access-requests.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bulk approval failed. Please try again.');
        }
    }

    /**
     * Retry failed Moodle sync for a request
     */
    public function retrySync(CourseAccessRequest $courseAccessRequest)
    {
        $this->authorize('approve', $courseAccessRequest);

        if (!$courseAccessRequest->hasSyncFailed()) {
            return back()->with('error', 'This request does not have a failed sync.');
        }

        $user = $courseAccessRequest->user;
        $course = $courseAccessRequest->course;

        // Re-queue the sync
        if (!$user->hasMoodleAccount()) {
            CreateOrLinkMoodleUser::dispatch($user)->chain([
                new EnrollUserIntoMoodleCourse($user, $course, $courseAccessRequest),
            ]);
        } else {
            EnrollUserIntoMoodleCourse::dispatch($user, $course, $courseAccessRequest);
        }

        $courseAccessRequest->markSyncing();

        return redirect()
            ->route('admin.course-access-requests.index')
            ->with('success', 'Sync retry queued.');
    }

    /**
     * Display the user's own course access requests (My Requests page)
     */
    public function userRequests(Request $request)
    {
        $user = auth()->user();

        // Get user's requests with filtering
        $status = $request->input('status', 'all');

        $query = CourseAccessRequest::where('user_id', $user->id)
            ->with(['course', 'approver'])
            ->orderBy('requested_at', 'desc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->paginate(10);

        // Get counts for tabs
        $counts = [
            'all' => CourseAccessRequest::where('user_id', $user->id)->count(),
            'pending' => CourseAccessRequest::where('user_id', $user->id)->pending()->count(),
            'approved' => CourseAccessRequest::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => CourseAccessRequest::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return view('my-requests.index', compact('requests', 'counts', 'status'));
    }

    /**
     * User endpoint: Create a course access request
     */
    public function store(Request $request, Course $course)
    {
        $user = auth()->user();

        $this->authorize('requestAccess', $course);

        $validated = $request->validate([
            'request_reason' => 'nullable|string|max:1000',
        ]);

        // Check if request already exists
        $existingRequest = CourseAccessRequest::findForUserAndCourse($user->id, $course->id);

        if ($existingRequest) {
            if ($existingRequest->isPending()) {
                return back()->with('info', 'You already have a pending request for this course.');
            }

            if ($existingRequest->isApproved()) {
                return back()->with('info', 'You already have access to this course.');
            }

            if ($existingRequest->isRejected()) {
                // Allow re-request - update the existing request
                $existingRequest->update([
                    'status' => CourseAccessRequest::STATUS_PENDING,
                    'request_reason' => $validated['request_reason'] ?? null,
                    'rejection_reason' => null,
                    'admin_notes' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                    'moodle_sync_status' => CourseAccessRequest::SYNC_NOT_SYNCED,
                    'moodle_sync_error' => null,
                    'moodle_sync_attempts' => 0,
                    'requested_at' => now(),
                ]);

                SystemNotification::notifyNewCourseRequest($existingRequest);

                return back()->with('success', 'Your access request has been resubmitted.');
            }
        }

        // Create new request
        $accessRequest = CourseAccessRequest::createRequest(
            $user,
            $course,
            $validated['request_reason'] ?? null
        );

        // Notify Course Admins
        SystemNotification::notifyNewCourseRequest($accessRequest);

        // Log the action
        ActivityLogger::log(
            'course_access_requested',
            "Requested access to '{$course->title}'",
            $accessRequest,
            [
                'course_id' => $course->id,
            ],
            'success',
            'info'
        );

        return back()->with('success', 'Your access request has been submitted. You will be notified once it is reviewed.');
    }
}
