<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCourseCreator
{
    /**
     * Handle an incoming request.
     * Only allows users who are marked as course creators.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Superadmins always have access
        if ($user->hasRole('superadmin')) {
            return $next($request);
        }

        // Check if user is a course creator
        if (!$user->is_course_creator) {
            abort(403, 'You do not have permission to create courses. Contact your administrator to request course creator access.');
        }

        return $next($request);
    }
}
