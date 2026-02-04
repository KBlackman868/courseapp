<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckCourseAdmin Middleware
 *
 * Controls access to course management pages (course access requests, etc.).
 *
 * WHO CAN PASS THIS MIDDLEWARE:
 * - SuperAdmin (always has full access)
 * - Admin users (all admins can view/manage course access requests)
 * - Users with the course_admin role
 *
 * WHY ALLOW ALL ADMINS:
 * The navigation menu shows course management links to all admin-level users.
 * Blocking regular admins here would create a confusing experience where they
 * see the link but get a 403 error when clicking it.
 *
 * USAGE IN ROUTES:
 * Route::middleware('course.admin')->group(function () { ... });
 */
class CheckCourseAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'error' => 'You must be logged in to access this resource.'
                ], 401);
            }

            return redirect()->route('login');
        }

        // SuperAdmin always has full access
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // All Admin users can access course management pages
        // This matches the navigation which shows the link to all admins
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Users with explicit course_admin role
        if ($user->isCourseAdmin()) {
            return $next($request);
        }

        // User doesn't have sufficient permission
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Forbidden.',
                'error' => 'You do not have permission to manage course access requests.'
            ], 403);
        }

        // Redirect to dashboard with error message
        return redirect()->route('dashboard')->with('error',
            'You do not have permission to access that page. Admin or Course Admin role is required.'
        );
    }
}
