<?php

namespace App\Helpers;

use App\Models\User;

/**
 * NavConfig - Single Source of Truth for Navigation
 *
 * This class controls ALL navigation labels/links across the application.
 * It ensures consistency between public and authenticated navigation.
 *
 * Usage:
 *   $nav = NavConfig::get($user);
 *   // or for guests:
 *   $nav = NavConfig::get(null);
 */
class NavConfig
{
    /**
     * Get navigation configuration based on user role and permissions
     *
     * @param User|null $user
     * @return array
     */
    public static function get(?User $user = null): array
    {
        $isAuthenticated = $user !== null;
        $role = $isAuthenticated ? self::getPrimaryRole($user) : 'guest';
        $isCourseAdmin = $isAuthenticated && $user->isCourseAdmin();

        return [
            'is_authenticated' => $isAuthenticated,
            'role' => $role,
            'is_course_admin' => $isCourseAdmin,
            'main_nav' => self::getMainNav($isAuthenticated, $role, $isCourseAdmin),
            'user_nav' => $isAuthenticated ? self::getUserNav($user) : [],
            'public_nav' => self::getPublicNav($isAuthenticated),
        ];
    }

    /**
     * Get the primary role for navigation purposes
     */
    private static function getPrimaryRole(User $user): string
    {
        if ($user->isSuperAdmin()) {
            return 'superadmin';
        }
        if ($user->isAdmin()) {
            return 'admin';
        }
        // Check for course_admin role
        if ($user->hasRole('course_admin')) {
            return 'course_admin';
        }
        if ($user->isMohStaff()) {
            return 'moh_staff';
        }
        if ($user->isExternalUser()) {
            return 'external_user';
        }
        return 'user';
    }

    /**
     * Get main navigation items based on role
     */
    private static function getMainNav(bool $isAuthenticated, string $role, bool $isCourseAdmin): array
    {
        // Guest navigation
        if (!$isAuthenticated) {
            return [
                ['label' => 'Home', 'route' => 'home', 'icon' => 'home'],
                ['label' => 'Login', 'route' => 'login', 'icon' => 'login'],
                ['label' => 'External Account', 'route' => 'register.external', 'icon' => 'user-plus'],
                ['label' => 'MOH Staff Request', 'route' => 'moh.request-account', 'icon' => 'building'],
            ];
        }

        // Base navigation for all authenticated users
        $nav = [
            ['label' => 'Home', 'route' => 'home', 'icon' => 'home'],
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
        ];

        // Role-specific navigation
        switch ($role) {
            case 'superadmin':
                return array_merge($nav, self::getSuperAdminNav());

            case 'admin':
                return array_merge($nav, self::getAdminNav());

            case 'course_admin':
                return array_merge($nav, self::getCourseAdminNav());

            case 'moh_staff':
            case 'external_user':
            default:
                return array_merge($nav, self::getLearnerNav());
        }
    }

    /**
     * SuperAdmin navigation items
     * SuperAdmins have access to ALL system functions including role management
     */
    private static function getSuperAdminNav(): array
    {
        return [
            // SuperAdmin-only dashboard
            ['label' => 'SuperAdmin', 'route' => 'dashboard.superadmin', 'icon' => 'shield-check', 'superadmin_only' => true],
            // Role management - ONLY visible to SuperAdmins
            ['label' => 'Roles', 'route' => 'admin.roles.index', 'icon' => 'key', 'superadmin_only' => true, 'permission' => 'roles.manage'],
            ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'users', 'permission' => 'users.view'],
            ['label' => 'Course Management', 'route' => 'courses.index', 'icon' => 'book-open', 'permission' => 'courses.manage'],
            [
                'label' => 'Pending',
                'icon' => 'clock',
                'badge' => 'pending_count',
                'permission' => 'users.approve',
                'children' => [
                    ['label' => 'Account Requests', 'route' => 'admin.account-requests.index', 'badge' => 'account_pending'],
                    ['label' => 'Course Access', 'route' => 'admin.course-access-requests.index', 'badge' => 'course_pending'],
                    ['label' => 'Legacy Enrollments', 'route' => 'admin.enrollments.index'],
                ],
            ],
            ['label' => 'Moodle', 'route' => 'admin.moodle.status', 'icon' => 'server', 'permission' => 'system.moodle'],
            ['label' => 'Audit Log', 'route' => 'admin.activity-logs.index', 'icon' => 'clipboard-list', 'permission' => 'system.logs'],
            ['label' => 'Notifications', 'route' => 'notifications.index', 'icon' => 'bell', 'badge' => 'unread_notifications'],
        ];
    }

    /**
     * Admin navigation items
     * Admins can manage users and enrollments but NOT roles
     */
    private static function getAdminNav(): array
    {
        return [
            // Note: NO Roles link for Admins - they cannot manage roles
            ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'users', 'permission' => 'users.view'],
            ['label' => 'Course Management', 'route' => 'courses.index', 'icon' => 'book-open', 'permission' => 'courses.view'],
            [
                'label' => 'Pending',
                'icon' => 'clock',
                'badge' => 'pending_count',
                'permission' => 'enrollments.view',
                'children' => [
                    ['label' => 'Account Requests', 'route' => 'admin.account-requests.index', 'badge' => 'account_pending'],
                    ['label' => 'Course Access', 'route' => 'admin.course-access-requests.index', 'badge' => 'course_pending'],
                    ['label' => 'Legacy Enrollments', 'route' => 'admin.enrollments.index'],
                ],
            ],
            ['label' => 'Notifications', 'route' => 'notifications.index', 'icon' => 'bell', 'badge' => 'unread_notifications'],
        ];
    }

    /**
     * Course Admin navigation items
     * Course Admins can manage courses and approve users but NOT roles
     */
    private static function getCourseAdminNav(): array
    {
        return [
            // Note: NO Roles link for Course Admins - they cannot manage roles
            ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'users', 'permission' => 'users.view'],
            ['label' => 'Course Management', 'route' => 'courses.index', 'icon' => 'book-open', 'permission' => 'courses.manage'],
            [
                'label' => 'Pending',
                'icon' => 'clock',
                'badge' => 'pending_count',
                'permission' => 'users.approve',
                'children' => [
                    ['label' => 'Account Requests', 'route' => 'admin.account-requests.index', 'badge' => 'account_pending'],
                    ['label' => 'Course Access', 'route' => 'admin.course-access-requests.index', 'badge' => 'course_pending'],
                    ['label' => 'Legacy Enrollments', 'route' => 'admin.enrollments.index'],
                ],
            ],
            ['label' => 'Notifications', 'route' => 'notifications.index', 'icon' => 'bell', 'badge' => 'unread_notifications'],
        ];
    }

    /**
     * Learner navigation items (MOH Staff / External User)
     */
    private static function getLearnerNav(): array
    {
        return [
            ['label' => 'Courses', 'route' => 'dashboard.learner', 'icon' => 'academic-cap'],
            ['label' => 'My Learning', 'route' => 'my-learning.index', 'icon' => 'play'],
            ['label' => 'My Requests', 'route' => 'my-requests.index', 'icon' => 'document-text'],
            ['label' => 'Notifications', 'route' => 'notifications.index', 'icon' => 'bell', 'badge' => 'unread_notifications'],
        ];
    }

    /**
     * Public navigation (always visible)
     */
    private static function getPublicNav(bool $isAuthenticated): array
    {
        $nav = [
            ['label' => 'Home', 'route' => 'home', 'icon' => 'home'],
        ];

        if (!$isAuthenticated) {
            $nav[] = ['label' => 'Login', 'route' => 'login', 'icon' => 'login'];
        }

        return $nav;
    }

    /**
     * User dropdown navigation
     */
    private static function getUserNav(User $user): array
    {
        return [
            ['label' => 'Profile', 'route' => 'profile.show', 'icon' => 'user'],
            ['label' => 'Settings', 'route' => 'profile.settings', 'icon' => 'cog'],
            ['label' => 'divider'],
            ['label' => 'Logout', 'route' => 'logout', 'icon' => 'logout', 'method' => 'POST'],
        ];
    }

    /**
     * Get badge counts for navigation
     */
    public static function getBadgeCounts(?User $user = null): array
    {
        if (!$user) {
            return [];
        }

        $counts = [
            'unread_notifications' => $user->systemNotifications()->unread()->count(),
        ];

        // Admin/Course Admin badge counts
        if ($user->isSuperAdmin() || $user->isCourseAdmin()) {
            $counts['account_pending'] = \App\Models\AccountRequest::pending()->count();
            $counts['course_pending'] = \App\Models\CourseAccessRequest::pending()->count();
            $counts['pending_count'] = $counts['account_pending'] + $counts['course_pending'];
        }

        return $counts;
    }

    /**
     * Check if a route is active
     */
    public static function isActive(string $routeName): bool
    {
        return request()->routeIs($routeName) || request()->routeIs($routeName . '.*');
    }

    /**
     * Get dashboard redirect URL based on role
     */
    public static function getDashboardUrl(?User $user = null): string
    {
        if (!$user) {
            return route('login');
        }

        // SuperAdmin → SuperAdmin Dashboard
        if ($user->isSuperAdmin()) {
            return route('dashboard.superadmin');
        }

        // Admin, Course Admin → Admin Dashboard
        if ($user->isAdmin() || $user->hasRole('course_admin')) {
            return route('dashboard.admin');
        }

        // MOH Staff, External User → Learner Dashboard
        return route('dashboard.learner');
    }

    /**
     * Check if user has permission (with SuperAdmin override)
     * SuperAdmins automatically have all permissions.
     */
    public static function hasPermission(User $user, string $permission): bool
    {
        // SuperAdmins have all permissions
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can($permission);
    }

    /**
     * Check if a nav item should be visible to user
     */
    public static function canAccessNavItem(User $user, array $navItem): bool
    {
        // SuperAdmin-only items
        if (isset($navItem['superadmin_only']) && $navItem['superadmin_only']) {
            return $user->isSuperAdmin();
        }

        // Permission-based check
        if (isset($navItem['permission'])) {
            return self::hasPermission($user, $navItem['permission']);
        }

        // Default: allow
        return true;
    }
}
