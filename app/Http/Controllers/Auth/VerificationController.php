<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Email Verification Controller
 *
 * Handles OTP-based email verification for authenticated users.
 * This replaces Laravel's default link-based email verification.
 */
class VerificationController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Display the email verification notice.
     *
     * For authenticated users who haven't verified their email,
     * this either shows the OTP verification form or initiates OTP.
     */
    public function notice(Request $request)
    {
        $user = Auth::user();

        // Already verified - redirect to dashboard
        if ($user && $user->hasVerifiedEmail() && $user->initial_otp_completed) {
            return redirect()->route('dashboard')
                ->with('info', 'Your email is already verified.');
        }

        // User has active OTP - redirect to OTP form
        if ($user && $user->otp_code && $user->otp_expires_at && $user->otp_expires_at->isFuture()) {
            session(['otp_user_id' => $user->id]);
            return redirect()->route('auth.otp.verify')
                ->with('info', 'Please enter the verification code sent to your email.');
        }

        // Show verification notice with option to send OTP
        return view('auth.verify-email-otp', [
            'user' => $user,
            'canResend' => $user ? $user->canRequestVerification() : false,
            'secondsUntilResend' => $user ? $user->seconds_until_can_request : 0,
        ]);
    }

    /**
     * Initiate OTP verification for logged-in user.
     */
    public function initiateOtp(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login to verify your email.');
        }

        if ($user->hasVerifiedEmail() && $user->initial_otp_completed) {
            return redirect()->route('dashboard')
                ->with('info', 'Your email is already verified.');
        }

        // Check rate limiting
        if (!$user->canRequestVerification()) {
            return back()->with('error', 'Please wait before requesting another verification code.');
        }

        // Send OTP
        $result = $this->otpService->sendOtp($user);

        if (!$result['success']) {
            Log::warning('Failed to send OTP for verification', [
                'user_id' => $user->id,
                'error' => $result['message']
            ]);

            return back()->with('error', $result['message']);
        }

        // Store user ID in session for OTP verification
        session(['otp_user_id' => $user->id]);

        if (class_exists(ActivityLogger::class)) {
            ActivityLogger::logAuth('otp_initiated', 'User initiated email verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);
        }

        return redirect()->route('auth.otp.verify')
            ->with('success', 'Verification code sent to your email.');
    }

    /**
     * Resend OTP code.
     */
    public function resendOtp(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Not authenticated.'], 401);
            }
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail() && $user->initial_otp_completed) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Already verified.']);
            }
            return redirect()->route('dashboard');
        }

        $result = $this->otpService->sendOtp($user);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            session(['otp_user_id' => $user->id]);
            return redirect()->route('auth.otp.verify')
                ->with('success', 'New verification code sent to your email.');
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Handle legacy email verification links.
     *
     * When a user clicks an old-style verification link from their email,
     * we redirect them to the OTP verification flow instead.
     */
    public function handleLegacyVerification(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Verify the hash matches the user's email
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // If user is already verified, redirect to dashboard
        if ($user->hasVerifiedEmail() && $user->initial_otp_completed) {
            // Log them in if not already
            if (!Auth::check()) {
                Auth::login($user);
            }
            return redirect()->route('dashboard')
                ->with('success', 'Your email is already verified.');
        }

        // Log the user in if they aren't already
        if (!Auth::check()) {
            Auth::login($user);
        }

        // Send OTP to complete verification
        $result = $this->otpService->sendOtp($user);

        if ($result['success']) {
            session(['otp_user_id' => $user->id]);

            if (class_exists(ActivityLogger::class)) {
                ActivityLogger::logAuth('legacy_verification_redirect', 'User clicked legacy email link, redirected to OTP', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip_address' => $request->ip()
                ]);
            }

            return redirect()->route('auth.otp.verify')
                ->with('info', 'For security, please enter the verification code sent to your email.');
        }

        // OTP send failed - show error
        return redirect()->route('verification.notice')
            ->with('error', 'Failed to send verification code. Please try again.');
    }
}
