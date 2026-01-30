<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AccountRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * AccountRequestPolicy
 *
 * This policy controls who can manage account requests.
 *
 * Account requests are submitted by MOH Staff during registration.
 * They need to be approved by Course Admin before the user can access the system.
 */
class AccountRequestPolicy
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
     * Determine whether the user can view the list of account requests.
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->canApproveAccounts();
    }

    /**
     * Determine whether the user can view a specific account request.
     */
    public function view(User $authUser, AccountRequest $request): bool
    {
        return $authUser->canApproveAccounts();
    }

    /**
     * Determine whether the user can approve an account request.
     */
    public function approve(User $authUser, AccountRequest $request): bool
    {
        // Can only approve pending requests
        if (!$request->isPending()) {
            return false;
        }

        return $authUser->canApproveAccounts();
    }

    /**
     * Determine whether the user can reject an account request.
     */
    public function reject(User $authUser, AccountRequest $request): bool
    {
        // Can only reject pending requests
        if (!$request->isPending()) {
            return false;
        }

        return $authUser->canApproveAccounts();
    }

    /**
     * Determine whether the user can bulk approve account requests.
     */
    public function bulkApprove(User $authUser): bool
    {
        return $authUser->canApproveAccounts();
    }

    /**
     * Determine whether the user can delete an account request.
     */
    public function delete(User $authUser, AccountRequest $request): bool
    {
        // Only SuperAdmin can delete requests
        // This will always return false because of before() check
        return false;
    }
}
