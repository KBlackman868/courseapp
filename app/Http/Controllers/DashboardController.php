<?php

namespace App\Http\Controllers;

use App\Helpers\NavConfig;
use App\Models\AccountRequest;
use App\Models\Course;
use App\Models\CourseAccessRequest;
use App\Models\Enrollment;
use App\Models\SystemNotification;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 *
 * Handles role-aware dashboard redirects and views.
 *
 * ROUTING MODEL:
 * - /dashboard → role-aware redirect
 * - /dashboard/admin → SuperAdmin/Admin/Course Admin view
 * - /dashboard/learner → MOH Staff/External User view
 *
 * IMPORTANT: SuperAdmin/Admin/Course Admin must NOT see course listings.
 * Their dashboard focuses on pending workloads, Moodle sync health, and audit.
 */
class DashboardController extends Controller
{
    /**
     * Main dashboard entry point - redirects based on role
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Check if user is pending approval (MOH Staff)
        if ($this->isPendingApproval($user)) {
            return redirect()->route('account.pending');
        }

        // Role-aware redirect
        $dashboardUrl = NavConfig::getDashboardUrl($user);

        return redirect($dashboardUrl);
    }

    /**
     * Admin Dashboard (SuperAdmin/Admin/Course Admin)
     *
     * Shows:
     * - Pending workloads (account requests, course access requests)
     * - Moodle sync health
     * - Recent activity/audit
     * - Notifications
     *
     * Does NOT show: Course listings
     */
    public function admin(Request $request)
    {
        $user = auth()->user();

        // Ensure user has admin access
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            return redirect()->route('dashboard.learner');
        }

        // Get pending counts
        $pendingAccountRequests = AccountRequest::pending()->count();
        $pendingCourseRequests = CourseAccessRequest::pending()->count();
        $failedSyncs = CourseAccessRequest::syncFailed()->count();

        // Get recent pending items for quick access
        $recentAccountRequests = AccountRequest::pending()
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentCourseRequests = CourseAccessRequest::pending()
            ->with(['user', 'course'])
            ->orderBy('requested_at', 'desc')
            ->limit(5)
            ->get();

        // Get failed Moodle syncs
        $failedMoodleSyncs = CourseAccessRequest::syncFailed()
            ->with(['user', 'course'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Get stats for cards
        $stats = [
            'total_users' => User::count(),
            'moh_staff' => User::whereHas('roles', fn($q) => $q->where('name', User::ROLE_MOH_STAFF))->count(),
            'external_users' => User::whereHas('roles', fn($q) => $q->where('name', User::ROLE_EXTERNAL_USER))->count(),
            'active_courses' => Course::active()->count(),
            'pending_total' => $pendingAccountRequests + $pendingCourseRequests,
            'failed_syncs' => $failedSyncs,
        ];

        // Get recent notifications for admin
        $recentNotifications = $user->systemNotifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Navigation config
        $navConfig = NavConfig::get($user);
        $badgeCounts = NavConfig::getBadgeCounts($user);

        return view('dashboard.admin', compact(
            'user',
            'stats',
            'pendingAccountRequests',
            'pendingCourseRequests',
            'recentAccountRequests',
            'recentCourseRequests',
            'failedMoodleSyncs',
            'recentNotifications',
            'navConfig',
            'badgeCounts'
        ));
    }

    /**
     * Learner Dashboard (MOH Staff / External User)
     *
     * Shows:
     * - Tabbed course experience (MOH Courses / External Courses)
     * - Search and filters
     * - Course cards with appropriate CTAs
     * - Onboarding banner (first login)
     */
    public function learner(Request $request)
    {
        $user = auth()->user();

        // Check if user is pending approval
        if ($this->isPendingApproval($user)) {
            return redirect()->route('account.pending');
        }

        // Get active tab (default based on user type)
        $defaultTab = $user->isMohStaff() ? 'moh' : 'external';
        $activeTab = $request->input('tab', $defaultTab);

        // Search and filter parameters
        $search = $request->input('search');
        $category = $request->input('category');
        $enrollmentType = $request->input('enrollment_type');

        // Build course query based on active tab
        $coursesQuery = Course::active()
            ->with(['category', 'creator']);

        if ($activeTab === 'moh') {
            // MOH tab: MOH_ONLY + BOTH
            $coursesQuery->whereIn('audience_type', [
                Course::AUDIENCE_MOH_ONLY,
                Course::AUDIENCE_MOH,
                Course::AUDIENCE_BOTH,
                Course::AUDIENCE_ALL,
            ]);
        } else {
            // External tab: EXTERNAL_ONLY + BOTH
            $coursesQuery->whereIn('audience_type', [
                Course::AUDIENCE_EXTERNAL_ONLY,
                Course::AUDIENCE_EXTERNAL,
                Course::AUDIENCE_BOTH,
                Course::AUDIENCE_ALL,
            ]);
        }

        // Apply search filter
        if ($search) {
            $coursesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($category) {
            $coursesQuery->where('category_id', $category);
        }

        // Apply enrollment type filter
        if ($enrollmentType) {
            $coursesQuery->where('enrollment_type', $enrollmentType);
        }

        // Get paginated courses
        $courses = $coursesQuery->orderBy('title')->paginate(12)->withQueryString();

        // Get user's enrollment/request status for each course
        $userCourseStatuses = $this->getUserCourseStatuses($user, $courses->pluck('id'));

        // Get categories for filter dropdown
        $categories = \App\Models\Category::orderBy('name')->pluck('name', 'id');

        // Check if user needs onboarding
        $showOnboarding = $this->shouldShowOnboarding($user);

        // Tab access control
        $canAccessMohTab = $user->isMohStaff() || $user->isSuperAdmin() || $user->isAdmin();
        $canAccessExternalTab = true; // Everyone can see external courses

        // Navigation config
        $navConfig = NavConfig::get($user);
        $badgeCounts = NavConfig::getBadgeCounts($user);

        return view('dashboard.learner', compact(
            'user',
            'courses',
            'userCourseStatuses',
            'activeTab',
            'search',
            'category',
            'enrollmentType',
            'categories',
            'showOnboarding',
            'canAccessMohTab',
            'canAccessExternalTab',
            'navConfig',
            'badgeCounts'
        ));
    }

    /**
     * Get user's enrollment/request status for a set of courses
     */
    private function getUserCourseStatuses(User $user, $courseIds): array
    {
        $statuses = [];

        // Get enrollments
        $enrollments = Enrollment::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy('course_id');

        // Get course access requests
        $accessRequests = CourseAccessRequest::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy('course_id');

        foreach ($courseIds as $courseId) {
            // Check enrollment first
            if ($enrollments->has($courseId)) {
                $enrollment = $enrollments->get($courseId);
                if ($enrollment->status === 'approved') {
                    $statuses[$courseId] = ['status' => 'enrolled', 'cta' => 'Go to Course', 'action' => 'access'];
                    continue;
                }
            }

            // Check access request
            if ($accessRequests->has($courseId)) {
                $request = $accessRequests->get($courseId);

                if ($request->isPending()) {
                    $statuses[$courseId] = ['status' => 'pending', 'cta' => 'Pending Approval', 'action' => null];
                    continue;
                }

                if ($request->isApproved()) {
                    if ($request->moodle_sync_status === CourseAccessRequest::SYNC_SYNCED) {
                        $statuses[$courseId] = ['status' => 'enrolled', 'cta' => 'Go to Course', 'action' => 'access'];
                    } elseif ($request->hasSyncFailed()) {
                        $statuses[$courseId] = ['status' => 'sync_failed', 'cta' => 'Processing Failed', 'action' => null];
                    } else {
                        $statuses[$courseId] = ['status' => 'syncing', 'cta' => 'Setting up...', 'action' => null];
                    }
                    continue;
                }

                if ($request->isRejected()) {
                    $statuses[$courseId] = ['status' => 'rejected', 'cta' => 'Request Again', 'action' => 'request'];
                    continue;
                }
            }

            // No enrollment or request - check enrollment type
            $statuses[$courseId] = ['status' => 'available', 'cta' => null, 'action' => null];
        }

        return $statuses;
    }

    /**
     * Check if user is pending approval (MOH Staff with pending account request)
     */
    private function isPendingApproval(User $user): bool
    {
        // Check if there's a pending account request for this email
        $pendingRequest = AccountRequest::where('email', $user->email)
            ->where('status', 'pending')
            ->exists();

        // Also check user status if you have one
        if ($user->status === 'pending') {
            return true;
        }

        return $pendingRequest;
    }

    /**
     * Check if user should see onboarding banner
     */
    private function shouldShowOnboarding(User $user): bool
    {
        // Show onboarding if onboarding_completed_at is null
        return is_null($user->onboarding_completed_at);
    }

    /**
     * Mark onboarding as completed (dismiss banner)
     */
    public function completeOnboarding(Request $request)
    {
        $user = auth()->user();
        $user->update(['onboarding_completed_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Welcome banner dismissed.');
    }

    /**
     * Account pending status page
     * Shown to MOH Staff whose account is pending approval
     */
    public function accountPending(Request $request)
    {
        $user = auth()->user();

        // Get the account request status
        $accountRequest = AccountRequest::where('email', $user->email)
            ->latest()
            ->first();

        $status = $accountRequest ? $accountRequest->status : 'pending';
        $requestedAt = $accountRequest ? $accountRequest->created_at : $user->created_at;

        return view('dashboard.account-pending', compact('user', 'status', 'requestedAt'));
    }
}
