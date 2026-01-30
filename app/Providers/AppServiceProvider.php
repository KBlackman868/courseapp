<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\AccountRequest;
use App\Models\CourseAccessRequest;
use App\Policies\UserPolicy;
use App\Policies\CoursePolicy;
use App\Policies\AccountRequestPolicy;
use App\Policies\CourseAccessRequestPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

public function boot(): void
{
    // Only force HTTPS when explicitly enabled via env
    if (config('app.force_https', false)) {
        URL::forceScheme('https');
        $this->app['request']->server->set('HTTPS', 'on');
    }

    // =========================================================================
    // REGISTER POLICIES
    // These control authorization throughout the application
    // =========================================================================
    Gate::policy(User::class, UserPolicy::class);
    Gate::policy(Course::class, CoursePolicy::class);
    Gate::policy(AccountRequest::class, AccountRequestPolicy::class);
    Gate::policy(CourseAccessRequest::class, CourseAccessRequestPolicy::class);

    // =========================================================================
    // DEFINE GATES FOR COMMON PERMISSION CHECKS
    // These provide convenient shortcuts for common authorization checks
    // =========================================================================

    // Gate: Can user manage courses? (SuperAdmin or Course Admin)
    Gate::define('manage-courses', function (User $user) {
        return $user->canManageCourses();
    });

    // Gate: Can user approve accounts? (SuperAdmin or Course Admin)
    Gate::define('approve-accounts', function (User $user) {
        return $user->canApproveAccounts();
    });

    // Gate: Can user approve course access? (SuperAdmin or Course Admin)
    Gate::define('approve-course-access', function (User $user) {
        return $user->canApproveCourseAccess();
    });

    // Gate: Can user view pending approvals? (SuperAdmin or Course Admin)
    Gate::define('view-pending-approvals', function (User $user) {
        return $user->canViewPendingApprovals();
    });

    // Gate: Can user assign Course Admin permission? (SuperAdmin only)
    Gate::define('assign-course-admin', function (User $user) {
        return $user->canAssignCourseAdminPermission();
    });

    // Share data with all views that use the layouts component
    View::composer('components.layouts', function ($view) {
        // Only calculate these for authenticated admin users
        if (auth()->check() && auth()->user()->hasRole(['admin', 'superadmin'])) {
            $view->with([
                'totalCourses' => Course::count(),
                'totalUsers' => User::count(),
                'pendingCount' => Enrollment::where('status', 'pending')->count(),
                // Add pending counts for Course Admin
                'pendingAccountRequests' => AccountRequest::pending()->count(),
                'pendingCourseAccessRequests' => CourseAccessRequest::pending()->count(),
            ]);
        }
    });

    Vite::prefetch(concurrency: 3);
}
}