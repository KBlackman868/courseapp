<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckCourseAdmin Middleware
 *
 * This middleware checks if the user has Course Admin permission.
 *
 * WHO CAN PASS THIS MIDDLEWARE:
 * - SuperAdmin (always has Course Admin capabilities)
 * - Users with course_admin role
 * - Admin users with is_course_admin flag = true
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

        // SuperAdmin always has Course Admin capabilities
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user is Admin with Course Admin permission
        if ($user->isCourseAdmin()) {
            return $next($request);
        }

        // User doesn't have Course Admin permission
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Forbidden.',
                'error' => 'You do not have Course Administrator permission.'
            ], 403);
        }

        // Redirect to dashboard with error message
        return redirect()->route('dashboard')->with('error',
            'You do not have permission to access that page. Course Administrator permission is required.'
        );
    }
}
