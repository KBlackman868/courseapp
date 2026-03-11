<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use App\Models\User;
use App\Models\SystemNotification;
use App\Services\ActivityLogger;
use App\Services\MoodleService;
use App\Jobs\CreateOrLinkMoodleUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

/**
 * AccountRequestController
 *
 * APPROVAL: Creates user account, assigns role, queues Moodle sync.
 * REJECTION: Permanently deletes ALL records (user, Moodle, account_request).
 *            No email sent to the rejected user.
 */
class AccountRequestController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', AccountRequest::class);

        $status = $request->input('status', 'pending');
        $department = $request->input('department');
        $search = $request->input('search');

        $query = AccountRequest::query()
            ->with('reviewer')
            ->orderBy('created_at', 'desc');

        if ($status === 'pending') {
            // Show all actionable pending states together
            $query->whereIn('status', [
                AccountRequest::STATUS_PENDING,
                AccountRequest::STATUS_PENDING_VERIFICATION,
                AccountRequest::STATUS_EMAIL_VERIFIED,
            ]);
        } elseif ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($department) {
            $query->where('department', $department);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(20);
        $departments = AccountRequest::getPendingDepartments();

        $counts = [
            'email_verified' => AccountRequest::where('status', AccountRequest::STATUS_EMAIL_VERIFIED)->count(),
            'pending_verification' => AccountRequest::where('status', AccountRequest::STATUS_PENDING_VERIFICATION)->count(),
            'pending' => AccountRequest::pending()->count(),
            'approved' => AccountRequest::where('status', AccountRequest::STATUS_APPROVED)->count(),
            'rejected' => AccountRequest::where('status', AccountRequest::STATUS_REJECTED)->count(),
            'all' => AccountRequest::count(),
        ];

        return Inertia::render('Admin/AccountRequests/Index', compact(
            'requests', 'departments', 'counts', 'status', 'department', 'search'
        ));
    }

    public function show(AccountRequest $accountRequest)
    {
        $this->authorize('view', $accountRequest);

        return Inertia::render('Admin/AccountRequests/Show', [
            'request' => $accountRequest->load('reviewer', 'user'),
        ]);
    }

    public function approve(Request $request, AccountRequest $accountRequest)
    {
        $this->authorize('approve', $accountRequest);

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $user = $accountRequest->approve(
                auth()->user(),
                $validated['admin_notes'] ?? null
            );

            try { CreateOrLinkMoodleUser::dispatch($user); } catch (\Exception $e) {
                Log::warning('Failed to dispatch Moodle sync', ['error' => $e->getMessage()]);
            }

            try { SystemNotification::notifyAccountApproved($user); } catch (\Exception $e) {
                Log::warning('Failed to send approval notification', ['error' => $e->getMessage()]);
            }

            ActivityLogger::log(
                'account_approved',
                "Approved account request for {$accountRequest->email}",
                $accountRequest,
                [
                    'user_id'      => $user->id,
                    'request_type' => $accountRequest->request_type,
                    'department'   => $accountRequest->department,
                    'approved_by'  => auth()->user()->email,
                ],
                'success',
                'info'
            );

            DB::commit();

            return redirect()
                ->route('admin.account-requests.index')
                ->with('success', "Account approved for {$accountRequest->full_name}. They have been notified and can now log in.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve account request', [
                'request_id' => $accountRequest->id,
                'error'      => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve account. Please try again.');
        }
    }

    /**
     * Reject — permanently deletes all related records.
     * No email sent to the rejected user.
     */
    public function reject(Request $request, AccountRequest $accountRequest)
    {
        $this->authorize('reject', $accountRequest);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'admin_notes'      => 'nullable|string|max:1000',
        ]);

        // Capture data for logging before deletion
        $logData = [
            'request_id'       => $accountRequest->id,
            'email'            => $accountRequest->email,
            'full_name'        => $accountRequest->full_name,
            'request_type'     => $accountRequest->request_type,
            'department'       => $accountRequest->department,
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_by'      => auth()->user()->email,
        ];

        DB::beginTransaction();

        try {
            // Delete user + Moodle account if they were created
            if ($accountRequest->user_id) {
                $user = User::find($accountRequest->user_id);
                if ($user) {
                    if ($user->moodle_user_id) {
                        try {
                            app(MoodleService::class)->deleteUser($user->moodle_user_id);
                            $logData['moodle_deleted'] = true;
                        } catch (\Exception $e) {
                            Log::warning('Failed to delete Moodle account', [
                                'moodle_user_id' => $user->moodle_user_id,
                                'error'          => $e->getMessage(),
                            ]);
                        }
                    }

                    // Delete all related records
                    $user->systemNotifications()->delete();
                    $user->courseAccessRequests()->delete();
                    $user->forceDelete();
                    $logData['user_deleted'] = true;
                }
            }

            // Log BEFORE deleting the request
            ActivityLogger::log(
                'account_rejected',
                "Rejected and permanently deleted account request for {$logData['email']}",
                null,
                $logData,
                'success',
                'warning'
            );

            // Permanently delete the account request
            $accountRequest->forceDelete();

            DB::commit();

            return redirect()
                ->route('admin.account-requests.index')
                ->with('success', "Account request for {$logData['full_name']} has been rejected and permanently removed.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject account request', [
                'request_id' => $accountRequest->id,
                'error'      => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to reject account. Please try again.');
        }
    }

    public function bulkApprove(Request $request)
    {
        $this->authorize('bulkApprove', AccountRequest::class);

        $validated = $request->validate([
            'request_ids'   => 'required|array',
            'request_ids.*' => 'exists:account_requests,id',
            'admin_notes'   => 'nullable|string|max:1000',
        ]);

        $successCount = 0;
        $failedCount = 0;

        DB::beginTransaction();

        try {
            foreach ($validated['request_ids'] as $requestId) {
                $accountRequest = AccountRequest::find($requestId);

                if ($accountRequest && $accountRequest->isPending()) {
                    try {
                        $user = $accountRequest->approve(
                            auth()->user(),
                            $validated['admin_notes'] ?? 'Bulk approved'
                        );

                        try { CreateOrLinkMoodleUser::dispatch($user); } catch (\Exception $e) {}
                        try { SystemNotification::notifyAccountApproved($user); } catch (\Exception $e) {}

                        $successCount++;
                    } catch (\Exception $e) {
                        $failedCount++;
                        Log::error('Failed to approve in bulk', [
                            'request_id' => $requestId,
                            'error'      => $e->getMessage(),
                        ]);
                    }
                }
            }

            try {
                ActivityLogger::log(
                    'bulk_account_approval',
                    "Bulk approved {$successCount} account requests",
                    null,
                    ['approved_count' => $successCount, 'failed_count' => $failedCount],
                    'success',
                    'info'
                );
            } catch (\Exception $e) {}

            DB::commit();

            $message = "Approved {$successCount} account(s).";
            if ($failedCount > 0) {
                $message .= " {$failedCount} failed.";
            }

            return redirect()->route('admin.account-requests.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bulk approval failed. Please try again.');
        }
    }

    public function bulkApproveAllMoh(Request $request)
    {
        $this->authorize('bulkApprove', AccountRequest::class);

        $validated = $request->validate(['department' => 'nullable|string']);

        $query = AccountRequest::awaitingApproval()->mohStaff();

        if (!empty($validated['department'])) {
            $query->forDepartment($validated['department']);
        }

        $request->merge(['request_ids' => $query->pluck('id')->toArray()]);

        return $this->bulkApprove($request);
    }
}
