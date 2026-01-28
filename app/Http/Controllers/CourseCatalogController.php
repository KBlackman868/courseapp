<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\EnrollmentRequest;
use App\Models\Enrollment;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseCatalogController extends Controller
{
    /**
     * Display the course catalog
     * Shows courses based on user type (internal/external)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get courses based on user type
        $coursesQuery = Course::active()
            ->forUser($user)
            ->with(['category', 'creator'])
            ->orderBy('title');

        // Get user's enrollment requests and enrollments
        $enrollmentRequests = EnrollmentRequest::forUser($user->id)
            ->get()
            ->keyBy('course_id');

        $enrollments = Enrollment::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            ->keyBy('course_id');

        $courses = $coursesQuery->get()->map(function ($course) use ($user, $enrollmentRequests, $enrollments) {
            $course->user_enrollment_status = $this->getEnrollmentStatus($course, $user, $enrollmentRequests, $enrollments);
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
     * Returns:
     * - 'open': Course is free, user can enter directly
     * - 'enrolled': User has approved enrollment
     * - 'pending': User has pending enrollment request
     * - 'denied': User's enrollment request was denied
     * - 'can_request': User can request access
     */
    private function getEnrollmentStatus($course, $user, $enrollmentRequests, $enrollments): string
    {
        // Check existing enrollment first
        if ($enrollments->has($course->id)) {
            $enrollment = $enrollments->get($course->id);
            if ($enrollment->status === 'approved') {
                return 'enrolled';
            }
            if ($enrollment->status === 'pending') {
                return 'pending';
            }
        }

        // Check enrollment requests
        if ($enrollmentRequests->has($course->id)) {
            $request = $enrollmentRequests->get($course->id);
            return $request->status; // 'pending', 'approved', 'denied'
        }

        // If course is free, user can access directly
        if ($course->is_free) {
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
