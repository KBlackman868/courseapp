<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CourseAccessRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * CourseAccessRequestPolicy
 *
 * This policy controls who can manage course access requests.
 *
 * SECURITY RULES:
 * - Users can only see their own requests (IDOR prevention)
 * - Course Admin can see all requests and approve/reject them
 * - Users cannot view/modify other users' requests
 */
class CourseAccessRequestPolicy
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
     * Determine whether the user can view the list of course access requests.
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->canApproveCourseAccess();
    }

    /**
     * Determine whether the user can view a specific course access request.
     *
     * Users can only view their own requests (IDOR prevention).
     */
    public function view(User $authUser, CourseAccessRequest $request): bool
    {
        // Users can view their own requests
        if ($request->user_id === $authUser->id) {
            return true;
        }

        // Course Admin can view all requests
        return $authUser->canApproveCourseAccess();
    }

    /**
     * Determine whether the user can create a course access request.
     */
    public function create(User $authUser): bool
    {
        // Any authenticated user can request access
        return true;
    }

    /**
     * Determine whether the user can approve a course access request.
     */
    public function approve(User $authUser, CourseAccessRequest $request): bool
    {
        // Can only approve pending requests
        if (!$request->isPending()) {
            return false;
        }

        // Cannot approve own request
        if ($request->user_id === $authUser->id) {
            return false;
        }

        return $authUser->canApproveCourseAccess();
    }

    /**
     * Determine whether the user can reject a course access request.
     */
    public function reject(User $authUser, CourseAccessRequest $request): bool
    {
        // Can only reject pending requests
        if (!$request->isPending()) {
            return false;
        }

        // Cannot reject own request
        if ($request->user_id === $authUser->id) {
            return false;
        }

        return $authUser->canApproveCourseAccess();
    }

    /**
     * Determine whether the user can revoke a course access request.
     */
    public function revoke(User $authUser, CourseAccessRequest $request): bool
    {
        // Can only revoke approved requests
        if (!$request->isApproved()) {
            return false;
        }

        return $authUser->canApproveCourseAccess();
    }

    /**
     * Determine whether the user can bulk approve course access requests.
     */
    public function bulkApprove(User $authUser): bool
    {
        return $authUser->canApproveCourseAccess();
    }

    /**
     * Determine whether the user can delete a course access request.
     *
     * Users can only delete their own pending requests.
     */
    public function delete(User $authUser, CourseAccessRequest $request): bool
    {
        // Users can delete their own pending requests
        if ($request->user_id === $authUser->id && $request->isPending()) {
            return true;
        }

        // Course Admin can delete any request
        return $authUser->canApproveCourseAccess();
    }
}
