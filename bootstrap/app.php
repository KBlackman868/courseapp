<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // CRITICAL: Configure TrustProxies FIRST for proper HTTPS detection behind reverse proxies
        // This fixes CSRF 419 errors when the app is behind IIS/Nginx with SSL termination
        $middleware->trustProxies(at: '*', headers:
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // Register route middleware aliases
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'verified' => \App\Http\Middleware\EnsureEmailVerified::class,
            'email.verified' => \App\Http\Middleware\EnsureEmailVerified::class,
            // Course Admin middleware - checks if user has Course Admin permission
            // Used for routes that require Course Admin access (approvals, course management)
            'course.admin' => \App\Http\Middleware\CheckCourseAdmin::class,
        ]);

        // Add any global middleware here if needed
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\CheckSuspended::class,
            \App\Http\Middleware\CheckAccountStatus::class,
            \App\Http\Middleware\LogActivity::class,
            \App\Http\Middleware\NoCacheHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function ($response, $exception, Request $request) {
            // For Inertia requests or regular web requests with HTTP exceptions,
            // render a proper error page instead of blank/JSON responses
            if ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();
                $errorStatuses = [401, 403, 404, 419, 429, 500, 503];

                if (in_array($status, $errorStatuses)) {
                    return Inertia::render('Error', ['status' => $status])
                        ->toResponse($request)
                        ->setStatusCode($status);
                }
            }

            // For 500-level errors on Inertia requests (non-HTTP exceptions)
            if ($request->header('X-Inertia') && !($exception instanceof HttpExceptionInterface)) {
                return Inertia::render('Error', ['status' => 500])
                    ->toResponse($request)
                    ->setStatusCode(500);
            }

            return $response;
        });
    })
    ->create();