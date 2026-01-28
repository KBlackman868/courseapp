<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserApprovalController extends Controller
{
    /**
     * Display pending user registrations
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = User::query()
            ->where('user_type', User::TYPE_EXTERNAL)
            ->orderBy('created_at', 'desc');

        if ($status === 'pending') {
            $query->pendingApproval();
        } elseif ($status === 'active') {
            $query->activeAccounts();
        } elseif ($status === 'inactive') {
            $query->inactiveAccounts();
        }

        $users = $query->paginate(20);

        // Get counts for tabs
        $counts = [
            'pending' => User::where('user_type', User::TYPE_EXTERNAL)->pendingApproval()->count(),
            'active' => User::where('user_type', User::TYPE_EXTERNAL)->activeAccounts()->count(),
            'inactive' => User::where('user_type', User::TYPE_EXTERNAL)->inactiveAccounts()->count(),
        ];

        ActivityLogger::logSystem('pending_users_viewed',
            "Admin viewed {$status} external users",
            [
                'status_filter' => $status,
                'count' => $users->total(),
                'admin' => auth()->user()->email,
            ]
        );

        return view('admin.users.pending', compact('users', 'status', 'counts'));
    }

    /**
     * Approve a pending user account
     */
    public function approve(Request $request, User $user)
    {
        if ($user->account_status !== User::STATUS_PENDING) {
            return back()->with('error', 'This user account is not pending approval.');
        }

        try {
            $user->update([
                'account_status' => User::STATUS_ACTIVE,
            ]);

            ActivityLogger::logUser('account_approved',
                "User account approved: {$user->email}",
                $user,
                [
                    'approved_by' => auth()->user()->email,
                    'user_type' => $user->user_type,
                    'organization' => $user->organization,
                ]
            );

            Log::info('User account approved', [
                'user_id' => $user->id,
                'email' => $user->email,
                'approved_by' => auth()->id(),
            ]);

            // TODO: Send welcome email notification
            // Mail::to($user->email)->send(new AccountApprovedEmail($user));

            return back()->with('success', "Account for {$user->full_name} ({$user->email}) has been approved.");

        } catch (\Exception $e) {
            Log::error('Failed to approve user account', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve account. Please try again.');
        }
    }

    /**
     * Deny a pending user account
     */
    public function deny(Request $request, User $user)
    {
        if ($user->account_status !== User::STATUS_PENDING) {
            return back()->with('error', 'This user account is not pending approval.');
        }

        $reason = $request->input('reason');

        try {
            $user->update([
                'account_status' => User::STATUS_INACTIVE,
            ]);

            ActivityLogger::logUser('account_denied',
                "User account denied: {$user->email}",
                $user,
                [
                    'denied_by' => auth()->user()->email,
                    'reason' => $reason,
                    'user_type' => $user->user_type,
                    'organization' => $user->organization,
                ],
                'success',
                'warning'
            );

            Log::info('User account denied', [
                'user_id' => $user->id,
                'email' => $user->email,
                'denied_by' => auth()->id(),
                'reason' => $reason,
            ]);

            // TODO: Send denial notification email
            // Mail::to($user->email)->send(new AccountDeniedEmail($user, $reason));

            return back()->with('success', "Account for {$user->full_name} ({$user->email}) has been denied.");

        } catch (\Exception $e) {
            Log::error('Failed to deny user account', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to deny account. Please try again.');
        }
    }

    /**
     * Deactivate an active user account
     */
    public function deactivate(Request $request, User $user)
    {
        if ($user->account_status === User::STATUS_INACTIVE) {
            return back()->with('error', 'This user account is already inactive.');
        }

        try {
            $oldStatus = $user->account_status;
            $user->update([
                'account_status' => User::STATUS_INACTIVE,
            ]);

            ActivityLogger::logUser('account_deactivated',
                "User account deactivated: {$user->email}",
                $user,
                [
                    'deactivated_by' => auth()->user()->email,
                    'old_status' => $oldStatus,
                ]
            );

            return back()->with('success', "Account for {$user->full_name} has been deactivated.");

        } catch (\Exception $e) {
            Log::error('Failed to deactivate user account', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to deactivate account. Please try again.');
        }
    }

    /**
     * Reactivate an inactive user account
     */
    public function reactivate(Request $request, User $user)
    {
        if ($user->account_status === User::STATUS_ACTIVE) {
            return back()->with('error', 'This user account is already active.');
        }

        try {
            $oldStatus = $user->account_status;
            $user->update([
                'account_status' => User::STATUS_ACTIVE,
            ]);

            ActivityLogger::logUser('account_reactivated',
                "User account reactivated: {$user->email}",
                $user,
                [
                    'reactivated_by' => auth()->user()->email,
                    'old_status' => $oldStatus,
                ]
            );

            return back()->with('success', "Account for {$user->full_name} has been reactivated.");

        } catch (\Exception $e) {
            Log::error('Failed to reactivate user account', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to reactivate account. Please try again.');
        }
    }
}
