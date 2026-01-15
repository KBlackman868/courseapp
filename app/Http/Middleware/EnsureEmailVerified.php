<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unified Email Verification Middleware
 *
 * This middleware replaces Laravel's default `EnsureEmailIsVerified` with a
 * custom OTP-based verification system. It ensures users cannot access any
 * protected routes until they have verified their email via OTP code.
 *
 * Key behaviors:
 * - Blocks ALL access (including navigation) until verified
 * - Redirects to OTP verification page if in verification flow
 * - Redirects to login if session expired
 * - Works with both new registrations and existing unverified users
 */
class EnsureEmailVerified
{
    /**
     * Routes that should be accessible during verification flow
     */
    protected array $allowedRoutes = [
        'auth.otp.verify',
        'auth.otp.submit',
        'auth.otp.resend',
        'logout',
        'verification.notice',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Not authenticated - let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Check if current route is allowed during verification
        $currentRoute = $request->route()?->getName();
        if ($currentRoute && in_array($currentRoute, $this->allowedRoutes)) {
            return $next($request);
        }

        // User is verified - allow access
        if ($this->isUserVerified($user)) {
            return $next($request);
        }

        // User is NOT verified - handle appropriately
        return $this->handleUnverifiedUser($request, $user);
    }

    /**
     * Check if user has completed email verification
     *
     * Uses BOTH email_verified_at AND initial_otp_completed for compatibility.
     * A user is considered verified if EITHER:
     * - email_verified_at is set (legacy users verified via link)
     * - initial_otp_completed is true (new OTP-based verification)
     *
     * For NEW users going forward, we require initial_otp_completed.
     */
    protected function isUserVerified($user): bool
    {
        // Primary check: OTP-based verification
        if ($user->initial_otp_completed) {
            return true;
        }

        // Secondary check: Email verified (for legacy compatibility)
        // AND user was created before OTP system was introduced
        if ($user->email_verified_at && $user->created_at < now()->subDays(1)) {
            return true;
        }

        return false;
    }

    /**
     * Handle unverified user access attempt
     */
    protected function handleUnverifiedUser(Request $request, $user): Response
    {
        // Store intended URL for post-verification redirect
        if (!$request->expectsJson()) {
            session()->put('url.intended', $request->url());
        }

        // Check if user is in active OTP verification flow
        if (session()->has('otp_user_id') && session('otp_user_id') === $user->id) {
            return $this->redirectToOtpVerification($request, 'Please complete your email verification.');
        }

        // Check if user has a pending OTP (code sent but not verified)
        if ($user->otp_code && $user->otp_expires_at && $user->otp_expires_at->isFuture()) {
            // Resume OTP verification flow
            session(['otp_user_id' => $user->id]);
            return $this->redirectToOtpVerification($request, 'Please enter the verification code sent to your email.');
        }

        // User needs to start OTP verification
        // Redirect to verification notice page which will initiate OTP
        return $this->redirectToVerificationNotice($request);
    }

    /**
     * Redirect to OTP verification page
     */
    protected function redirectToOtpVerification(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'verification_required' => true,
                'redirect' => route('auth.otp.verify')
            ], 403);
        }

        return redirect()->route('auth.otp.verify')
            ->with('warning', $message);
    }

    /**
     * Redirect to verification notice page
     */
    protected function redirectToVerificationNotice(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Email verification required.',
                'verification_required' => true,
                'redirect' => route('verification.notice')
            ], 403);
        }

        return redirect()->route('verification.notice')
            ->with('warning', 'Please verify your email address to access this page.');
    }
}
