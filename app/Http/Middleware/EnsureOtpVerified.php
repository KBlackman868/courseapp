<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    /**
     * Handle an incoming request.
     * Ensures that the user has completed their initial OTP verification.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has completed initial OTP verification
        if (!$user->initial_otp_completed) {
            // If user is in OTP verification flow (has pending OTP), redirect to verify
            if (session()->has('otp_user_id')) {
                return redirect()->route('auth.otp.verify')
                    ->with('warning', 'Please complete the verification process.');
            }

            // Otherwise, initiate OTP verification
            return redirect()->route('auth.otp.initiate')
                ->with('warning', 'Please verify your account to continue.');
        }

        return $next($request);
    }
}
