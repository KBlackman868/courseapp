<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ActivityLogger;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log page views and significant actions (skip static assets and AJAX polling)
        if ($this->shouldLog($request)) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Determine if the request should be logged
     */
    protected function shouldLog(Request $request): bool
    {
        // Skip static assets
        $skipExtensions = ['js', 'css', 'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'woff', 'woff2', 'ttf'];
        $extension = pathinfo($request->path(), PATHINFO_EXTENSION);
        if (in_array(strtolower($extension), $skipExtensions)) {
            return false;
        }

        // Skip AJAX polling requests (live updates)
        if ($request->ajax() && str_contains($request->path(), '/live')) {
            return false;
        }

        // Skip health checks
        if ($request->path() === 'up' || $request->path() === 'health') {
            return false;
        }

        // Only log authenticated user actions
        return auth()->check();
    }

    /**
     * Log the activity
     */
    protected function logActivity(Request $request, Response $response): void
    {
        try {
            $action = $this->determineAction($request);

            // Skip logging for very frequent actions
            if (in_array($action, ['api_call', 'polling'])) {
                return;
            }

            ActivityLogger::logSystem(
                $action,
                $this->buildDescription($request),
                [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'status_code' => $response->getStatusCode(),
                    'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                ]
            );
        } catch (\Exception $e) {
            // Silently fail - don't break the app if logging fails
            \Log::debug('Activity logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine the action type based on the request
     */
    protected function determineAction(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();

        // Auth actions
        if (str_contains($path, 'login')) return 'login_page';
        if (str_contains($path, 'logout')) return 'logout';
        if (str_contains($path, 'register')) return 'register_page';

        // Admin actions
        if (str_contains($path, 'admin/')) {
            if (str_contains($path, 'users')) return 'admin_users';
            if (str_contains($path, 'roles')) return 'admin_roles';
            if (str_contains($path, 'enrollments')) return 'admin_enrollments';
            if (str_contains($path, 'activity-logs')) return 'admin_logs';
            return 'admin_page';
        }

        // Course actions
        if (str_contains($path, 'courses')) {
            if ($method === 'POST' && str_contains($path, 'enroll')) return 'course_enroll';
            if ($method === 'POST') return 'course_create';
            if ($method === 'PUT' || $method === 'PATCH') return 'course_update';
            if ($method === 'DELETE') return 'course_delete';
            return 'course_view';
        }

        // Profile actions
        if (str_contains($path, 'profile')) return 'profile_view';

        // Default
        return $method === 'GET' ? 'page_view' : 'action';
    }

    /**
     * Build a description for the log entry
     */
    protected function buildDescription(Request $request): string
    {
        $user = auth()->user();
        $name = $user ? "{$user->first_name} {$user->last_name}" : 'Anonymous';

        return "{$name} accessed {$request->path()}";
    }
}
