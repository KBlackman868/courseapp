<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use App\Models\User;
use App\Models\SystemNotification;
use App\Services\ActivityLogger;
use App\Jobs\CreateOrLinkMoodleUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AccountRequestController
 *
 * Handles the approval workflow for account requests, especially from MOH Staff.
 *
 * WORKFLOW:
 * 1. User registers with @health.gov.tt email
 * 2. Account request created in "pending" status
 * 3. Course Admin sees request in pending queue
 * 4. Course Admin approves/rejects
 * 5. On approval: User account created, notification sent
 */
class AccountRequestController extends Controller
{
    /**
     * Display the list of account requests
     * Shows pending requests prominently at the top
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', AccountRequest::class);

        // Get filter parameters
        $status = $request->input('status', 'pending');
        $department = $request->input('department');
        $search = $request->input('search');

        // Build query
        $query = AccountRequest::query()
            ->with('reviewer')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($status && $status !== 'all') {
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

        // Get departments for filter dropdown
        $departments = AccountRequest::getPendingDepartments();

        // Get counts for tabs
        $counts = [
            'pending' => AccountRequest::pending()->count(),
            'approved' => AccountRequest::where('status', 'approved')->count(),
            'rejected' => AccountRequest::where('status', 'rejected')->count(),
            'all' => AccountRequest::count(),
        ];

        return view('admin.account-requests.index', compact(
            'requests', 'departments', 'counts', 'status', 'department', 'search'
        ));
    }

    /**
     * Show a specific account request
     */
    public function show(AccountRequest $accountRequest)
    {
        $this->authorize('view', $accountRequest);

        return view('admin.account-requests.show', [
            'request' => $accountRequest->load('reviewer', 'user'),
        ]);
    }

    /**
     * Approve an account request
     * Creates the user account and sends notification
     */
    public function approve(Request $request, AccountRequest $accountRequest)
    {
        $this->authorize('approve', $accountRequest);

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Approve the request (this creates the user)
            $user = $accountRequest->approve(
                auth()->user(),
                $validated['admin_notes'] ?? null
            );

            // Queue Moodle account creation for MOH staff
            if ($accountRequest->isMohStaffRequest()) {
                CreateOrLinkMoodleUser::dispatch($user);
            }

            // Send notification to the new user
            SystemNotification::notifyAccountApproved($user);

            // Log the action
            ActivityLogger::log(
                'account_approved',
                "Approved account request for {$accountRequest->email}",
                $accountRequest,
                [
                    'user_id' => $user->id,
                    'request_type' => $accountRequest->request_type,
                    'department' => $accountRequest->department,
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
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve account. Please try again.');
        }
    }

    /**
     * Reject an account request
     */
    public function reject(Request $request, AccountRequest $accountRequest)
    {
        $this->authorize('reject', $accountRequest);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $accountRequest->reject(
            auth()->user(),
            $validated['rejection_reason'],
            $validated['admin_notes'] ?? null
        );

        // Log the action
        ActivityLogger::log(
            'account_rejected',
            "Rejected account request for {$accountRequest->email}",
            $accountRequest,
            [
                'rejection_reason' => $validated['rejection_reason'],
                'request_type' => $accountRequest->request_type,
            ],
            'success',
            'warning'
        );

        // Note: We can't notify the user in-app since they don't have an account
        // An email notification should be sent instead

        return redirect()
            ->route('admin.account-requests.index')
            ->with('success', "Account request rejected for {$accountRequest->full_name}.");
    }

    /**
     * Bulk approve account requests
     */
    public function bulkApprove(Request $request)
    {
        $this->authorize('bulkApprove', AccountRequest::class);

        $validated = $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:account_requests,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $successCount = 0;
        $failedCount = 0;
        $approvedUsers = [];

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

                        // Queue Moodle account creation for MOH staff
                        if ($accountRequest->isMohStaffRequest()) {
                            CreateOrLinkMoodleUser::dispatch($user);
                        }

                        SystemNotification::notifyAccountApproved($user);
                        $approvedUsers[] = $user->id;
                        $successCount++;
                    } catch (\Exception $e) {
                        $failedCount++;
                        Log::error('Failed to approve account in bulk', [
                            'request_id' => $requestId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Log bulk action
            ActivityLogger::log(
                'bulk_account_approval',
                "Bulk approved {$successCount} account requests",
                null,
                [
                    'approved_count' => $successCount,
                    'failed_count' => $failedCount,
                    'affected_ids' => $approvedUsers,
                ],
                'success',
                'info'
            );

            DB::commit();

            $message = "Approved {$successCount} account(s).";
            if ($failedCount > 0) {
                $message .= " {$failedCount} failed.";
            }

            return redirect()
                ->route('admin.account-requests.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bulk approval failed. Please try again.');
        }
    }

    /**
     * Bulk approve all pending MOH staff requests (optionally filtered by department)
     */
    public function bulkApproveAllMoh(Request $request)
    {
        $this->authorize('bulkApprove', AccountRequest::class);

        $validated = $request->validate([
            'department' => 'nullable|string',
        ]);

        $query = AccountRequest::pending()->mohStaff();

        if (!empty($validated['department'])) {
            $query->forDepartment($validated['department']);
        }

        $pendingRequests = $query->pluck('id')->toArray();

        // Reuse bulkApprove logic
        $request->merge(['request_ids' => $pendingRequests]);

        return $this->bulkApprove($request);
    }
}
