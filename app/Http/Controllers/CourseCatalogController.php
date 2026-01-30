<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseAccessRequest;
use App\Models\Enrollment;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseCatalogController extends Controller
{
    /**
     * Display the course catalog
     * Shows courses based on user type (internal/external)
     *
     * Uses the new CourseAccessRequest system:
     * - OPEN_ENROLLMENT: Direct access
     * - APPROVAL_REQUIRED: Submit CourseAccessRequest for approval
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get courses based on user type
        $coursesQuery = Course::active()
            ->forUser($user)
            ->with(['category', 'creator'])
            ->orderBy('title');

        // Get user's course access requests (new system)
        $accessRequests = CourseAccessRequest::where('user_id', $user->id)
            ->get()
            ->keyBy('course_id');

        // Get user's enrollments (for legacy and direct enrollment tracking)
        $enrollments = Enrollment::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            ->keyBy('course_id');

        $courses = $coursesQuery->get()->map(function ($course) use ($user, $accessRequests, $enrollments) {
            $course->user_enrollment_status = $this->getEnrollmentStatus($course, $user, $accessRequests, $enrollments);
            return $course;
        });

        // Log catalog view
        ActivityLogger::logSystem('course_catalog_viewed',
            "User viewed course catalog",
            [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'courses_shown' => $courses->count(),
            ]
        );

        return view('courses.catalog', compact('courses'));
    }

    /**
     * Determine the enrollment status for a course and user
     *
     * Uses the new CourseAccessRequest system alongside legacy Enrollment model.
     *
     * Returns:
     * - 'open': Course is open enrollment, user can enter directly
     * - 'enrolled': User has approved enrollment
     * - 'pending': User has pending access request
     * - 'denied': User's access request was denied
     * - 'syncing': Access approved, Moodle sync in progress
     * - 'can_request': User can request access
     */
    private function getEnrollmentStatus($course, $user, $accessRequests, $enrollments): string
    {
        // Check existing enrollment (legacy support)
        if ($enrollments->has($course->id)) {
            $enrollment = $enrollments->get($course->id);
            if ($enrollment->status === 'approved') {
                return 'enrolled';
            }
            if ($enrollment->status === 'pending') {
                return 'pending';
            }
        }

        // Check course access requests (new system)
        if ($accessRequests->has($course->id)) {
            $request = $accessRequests->get($course->id);

            if ($request->isApproved()) {
                // Check Moodle sync status
                if ($request->moodle_sync_status === CourseAccessRequest::SYNC_SYNCED) {
                    return 'enrolled';
                }
                if ($request->moodle_sync_status === CourseAccessRequest::SYNC_SYNCING) {
                    return 'syncing';
                }
                if ($request->hasSyncFailed()) {
                    return 'sync_failed';
                }
                return 'approved';
            }

            if ($request->isPending()) {
                return 'pending';
            }

            if ($request->isRejected()) {
                return 'denied';
            }
        }

        // If course is open enrollment, user can access directly
        if ($course->isOpenEnrollment()) {
            return 'open';
        }

        // User can request access
        return 'can_request';
    }

    /**
     * Show a single course details
     */
    public function show(Course $course)
    {
        $user = Auth::user();

        // Check if user can view this course
        if (!$course->isVisibleTo($user)) {
            abort(403, 'You do not have permission to view this course.');
        }

        // Get enrollment status
        $enrollmentRequest = EnrollmentRequest::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $enrollmentStatus = 'can_request';

        if ($enrollment && $enrollment->status === 'approved') {
            $enrollmentStatus = 'enrolled';
        } elseif ($enrollment && $enrollment->status === 'pending') {
            $enrollmentStatus = 'pending';
        } elseif ($enrollmentRequest) {
            $enrollmentStatus = $enrollmentRequest->status;
        } elseif ($course->is_free) {
            $enrollmentStatus = 'open';
        }

        return view('courses.catalog-show', compact('course', 'enrollmentStatus', 'enrollmentRequest'));
    }
}
