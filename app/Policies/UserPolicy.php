<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * UserPolicy
 *
 * This policy controls who can do what with User records.
 *
 * SECURITY RULES:
 * - SuperAdmin can do everything
 * - Course Admin CANNOT see/search/access SuperAdmin users
 * - Course Admin can only change roles between MOH_Staff and External_User
 * - Regular users can only view their own profile
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * SuperAdmin bypasses all checks.
     */
    public function before(User $authUser, string $ability): ?bool
    {
        if ($authUser->isSuperAdmin()) {
            return true;
        }

        return null; // Fall through to specific checks
    }

    /**
     * Determine whether the user can view any users (user list).
     */
    public function viewAny(User $authUser): bool
    {
        // Admin or Course Admin can view user list
        return $authUser->isAdmin() || $authUser->isCourseAdmin();
    }

    /**
     * Determine whether the user can view a specific user.
     *
     * Course Admin CANNOT view SuperAdmin users.
     */
    public function view(User $authUser, User $targetUser): bool
    {
        // Users can always view themselves
        if ($authUser->id === $targetUser->id) {
            return true;
        }

        // Course Admin cannot view SuperAdmin
        if ($authUser->isCourseAdmin() && $targetUser->isSuperAdmin()) {
            return false;
        }

        // Admins and Course Admins can view others
        return $authUser->isAdmin() || $authUser->isCourseAdmin();
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $authUser): bool
    {
        return $authUser->isAdmin() || $authUser->isCourseAdmin();
    }

    /**
     * Determine whether the user can update a user.
     *
     * Course Admin CANNOT update SuperAdmin users.
     */
    public function update(User $authUser, User $targetUser): bool
    {
        // Users can update themselves (profile)
        if ($authUser->id === $targetUser->id) {
            return true;
        }

        // Course Admin cannot update SuperAdmin
        if ($authUser->isCourseAdmin() && $targetUser->isSuperAdmin()) {
            return false;
        }

        return $authUser->isAdmin() || $authUser->isCourseAdmin();
    }

    /**
     * Determine whether the user can delete a user.
     *
     * SuperAdmin CANNOT be deleted.
     * Course Admin CANNOT delete SuperAdmin.
     */
    public function delete(User $authUser, User $targetUser): bool
    {
        // Cannot delete SuperAdmin
        if ($targetUser->isSuperAdmin()) {
            return false;
        }

        // Cannot delete yourself
        if ($authUser->id === $targetUser->id) {
            return false;
        }

        return $authUser->isAdmin() || $authUser->isCourseAdmin();
    }

    /**
     * Determine whether the user can change a user's role.
     *
     * Course Admin can only change between MOH_Staff and External_User.
     */
    public function changeRole(User $authUser, User $targetUser, ?string $newRole = null): bool
    {
        // Cannot change SuperAdmin's role
        if ($targetUser->isSuperAdmin()) {
            return false;
        }

        // Cannot change your own role
        if ($authUser->id === $targetUser->id) {
            return false;
        }

        // If newRole is specified, validate it
        if ($newRole) {
            // Course Admin can only assign MOH_Staff or External_User
            if ($authUser->isCourseAdmin() && !$authUser->isSuperAdmin()) {
                $allowedRoles = [User::ROLE_MOH_STAFF, User::ROLE_EXTERNAL_USER];
                if (!in_array($newRole, $allowedRoles)) {
                    return false;
                }
            }
        }

        return $authUser->isAdmin() || $authUser->isCourseAdmin();
    }

    /**
     * Determine whether the user can assign the Course Admin permission.
     *
     * Only SuperAdmin can do this.
     */
    public function assignCourseAdmin(User $authUser, User $targetUser): bool
    {
        // Only SuperAdmin can assign Course Admin permission
        // This check will always fail because of the before() check
        // but we keep it for clarity
        return false;
    }

    /**
     * Determine whether the user can suspend/reactivate a user.
     */
    public function suspend(User $authUser, User $targetUser): bool
    {
        // Cannot suspend SuperAdmin
        if ($targetUser->isSuperAdmin()) {
            return false;
        }

        // Cannot suspend yourself
        if ($authUser->id === $targetUser->id) {
            return false;
        }

        return $authUser->isAdmin() || $authUser->isCourseAdmin();
    }

    /**
     * Determine whether the user can search for users.
     *
     * When Course Admin searches, SuperAdmin users should be excluded.
     */
    public function search(User $authUser): bool
    {
        return $authUser->isAdmin() || $authUser->isCourseAdmin();
    }
}
