<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * CoursePolicy
 *
 * This policy controls who can do what with courses.
 *
 * VISIBILITY RULES:
 * - MOH_ONLY courses: Only visible to MOH Staff
 * - EXTERNAL_ONLY courses: Only visible to External Users
 * - BOTH courses: Visible to everyone
 *
 * MANAGEMENT RULES:
 * - SuperAdmin and Course Admin can manage all courses
 * - Course creators can manage their own courses
 */
class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * SuperAdmin can do everything.
     */
    public function before(User $authUser, string $ability): ?bool
    {
        if ($authUser->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any courses (course list).
     */
    public function viewAny(User $authUser): bool
    {
        // Everyone can view the course list
        return true;
    }

    /**
     * Determine whether the user can view a specific course.
     */
    public function view(User $authUser, Course $course): bool
    {
        // Course Admin can view all courses
        if ($authUser->canManageCourses()) {
            return true;
        }

        // Check audience visibility
        return $course->isVisibleTo($authUser);
    }

    /**
     * Determine whether the user can create courses.
     */
    public function create(User $authUser): bool
    {
        return $authUser->canManageCourses() || $authUser->is_course_creator;
    }

    /**
     * Determine whether the user can update a course.
     */
    public function update(User $authUser, Course $course): bool
    {
        // Course Admin can update any course
        if ($authUser->canManageCourses()) {
            return true;
        }

        // Course creator can update their own course
        return $authUser->is_course_creator && $course->creator_id === $authUser->id;
    }

    /**
     * Determine whether the user can delete a course.
     */
    public function delete(User $authUser, Course $course): bool
    {
        return $authUser->canManageCourses();
    }

    /**
     * Determine whether the user can enroll in a course.
     */
    public function enroll(User $authUser, Course $course): bool
    {
        // Check if course is visible to user
        if (!$course->isVisibleTo($authUser)) {
            return false;
        }

        // For open enrollment, allow immediate enrollment
        if ($course->isOpenEnrollment()) {
            return true;
        }

        // For approval required, check if user can request access
        return true; // They'll need to request, but they can attempt
    }

    /**
     * Determine whether the user can request access to a course.
     */
    public function requestAccess(User $authUser, Course $course): bool
    {
        // Check if course is visible to user
        if (!$course->isVisibleTo($authUser)) {
            return false;
        }

        // Check if user already has a pending or approved request
        if ($course->hasPendingRequestFrom($authUser)) {
            return false;
        }

        if ($course->hasApprovedRequestFrom($authUser)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can access the course in Moodle.
     */
    public function accessMoodle(User $authUser, Course $course): bool
    {
        // Check if course has Moodle integration
        if (!$course->hasMoodleIntegration()) {
            return false;
        }

        // Check if user is enrolled and approved
        $enrollment = $course->enrollments()
            ->where('user_id', $authUser->id)
            ->where('status', 'approved')
            ->first();

        if ($enrollment) {
            return true;
        }

        // Check if user has approved course access request
        $accessRequest = $course->getAccessRequestFrom($authUser);
        if ($accessRequest && $accessRequest->isApproved()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage enrollments for a course.
     */
    public function manageEnrollments(User $authUser, Course $course): bool
    {
        return $authUser->canManageCourses();
    }

    /**
     * Determine whether the user can sync a course to Moodle.
     */
    public function syncToMoodle(User $authUser, Course $course): bool
    {
        return $authUser->canManageCourses();
    }
}
