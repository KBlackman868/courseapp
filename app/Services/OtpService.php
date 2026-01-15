<?php

namespace App\Services;

use App\Models\User;
use App\Mail\OtpVerificationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    private const OTP_LENGTH = 6;
    private const OTP_EXPIRY_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 30;
    private const MAX_RESENDS = 3;
    private const RESEND_WINDOW_MINUTES = 10;

    /**
     * Check if user needs OTP verification
     */
    public function needsOtpVerification(User $user): bool
    {
        // Check if the column exists and return appropriate value
        try {
            return !$user->initial_otp_completed;
        } catch (\Exception $e) {
            // Column might not exist yet - skip OTP
            Log::warning('initial_otp_completed column may not exist', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Generate and send OTP to user
     */
    public function sendOtp(User $user): array
    {
        try {
            // Check for rate limiting on resends
            $resendKey = "otp_resend_count_{$user->id}";
            $resendCount = Cache::get($resendKey, 0);

            if ($resendCount >= self::MAX_RESENDS) {
                return [
                    'success' => false,
                    'message' => 'Too many OTP requests. Please wait before requesting another.',
                    'wait_minutes' => self::RESEND_WINDOW_MINUTES
                ];
            }

            // Generate OTP
            $otp = $this->generateOtp();

            // Store OTP in user record
            try {
                $user->update([
                    'otp_code' => Hash::make($otp),
                    'otp_expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
                    'otp_verified' => false,
                    'otp_attempts' => 0,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to update user OTP fields', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                return [
                    'success' => false,
                    'message' => 'Failed to generate OTP. Database error.'
                ];
            }

            // Store plain OTP in cache for verification
            Cache::put(
                "otp_plain_{$user->id}",
                $otp,
                now()->addMinutes(self::OTP_EXPIRY_MINUTES)
            );

            // Increment resend counter
            Cache::put($resendKey, $resendCount + 1, now()->addMinutes(self::RESEND_WINDOW_MINUTES));

            // Send email
            try {
                // Check if mailable class exists
                if (class_exists(OtpVerificationEmail::class)) {
                    Mail::to($user->email)->send(new OtpVerificationEmail($user, $otp));
                } else {
                    // Fallback: send raw email
                    Mail::raw("Your MOH LMS verification code is: {$otp}\n\nThis code expires in 10 minutes.", function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Your MOH LMS Verification Code');
                    });
                }

                Log::info('OTP sent successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                return [
                    'success' => true,
                    'message' => 'OTP sent to your email address.',
                    'expires_in' => self::OTP_EXPIRY_MINUTES
                ];

            } catch (\Exception $e) {
                Log::error('Failed to send OTP email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send OTP email. Please try again.'
                ];
            }

        } catch (\Exception $e) {
            Log::error('OTP sendOtp failed', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ];
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(User $user, string $code): array
    {
        try {
            // Check if user is locked out
            if ($this->isLockedOut($user)) {
                $remainingMinutes = $this->getLockoutRemainingMinutes($user);
                return [
                    'success' => false,
                    'message' => "Too many failed attempts. Please try again in {$remainingMinutes} minutes.",
                    'locked' => true
                ];
            }

            // Check if OTP has expired
            if (!$user->otp_expires_at || $user->otp_expires_at->isPast()) {
                return [
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new one.',
                    'expired' => true
                ];
            }

            // Get plain OTP from cache
            $storedOtp = Cache::get("otp_plain_{$user->id}");

            // Verify OTP
            $isValid = $storedOtp && $code === $storedOtp;

            if (!$isValid) {
                // Increment attempts
                $user->increment('otp_attempts');

                if ($user->otp_attempts >= self::MAX_ATTEMPTS) {
                    $this->lockoutUser($user);
                    return [
                        'success' => false,
                        'message' => "Too many failed attempts. You are locked out for " . self::LOCKOUT_MINUTES . " minutes.",
                        'locked' => true
                    ];
                }

                $remainingAttempts = self::MAX_ATTEMPTS - $user->otp_attempts;
                return [
                    'success' => false,
                    'message' => "Invalid OTP. {$remainingAttempts} attempts remaining.",
                    'remaining_attempts' => $remainingAttempts
                ];
            }

            // OTP is valid - mark as verified
            try {
                $user->update([
                    'otp_verified' => true,
                    'otp_verified_at' => now(),
                    'otp_code' => null,
                    'otp_expires_at' => null,
                    'otp_attempts' => 0,
                    'initial_otp_completed' => true,
                    'initial_otp_completed_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to update user after OTP verification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Clear cache
            Cache::forget("otp_plain_{$user->id}");
            Cache::forget("otp_resend_count_{$user->id}");
            Cache::forget("otp_lockout_{$user->id}");

            Log::info('OTP verified successfully', ['user_id' => $user->id]);

            return [
                'success' => true,
                'message' => 'OTP verified successfully.'
            ];

        } catch (\Exception $e) {
            Log::error('OTP verifyOtp failed', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred during verification.'
            ];
        }
    }

    /**
     * Generate random OTP
     */
    private function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Check if user is locked out
     */
    private function isLockedOut(User $user): bool
    {
        return Cache::has("otp_lockout_{$user->id}");
    }

    /**
     * Lock out user after too many attempts
     */
    private function lockoutUser(User $user): void
    {
        Cache::put(
            "otp_lockout_{$user->id}",
            now(),
            now()->addMinutes(self::LOCKOUT_MINUTES)
        );

        Log::warning('User locked out due to too many OTP attempts', [
            'user_id' => $user->id
        ]);
    }

    /**
     * Get remaining lockout minutes
     */
    private function getLockoutRemainingMinutes(User $user): int
    {
        $lockoutTime = Cache::get("otp_lockout_{$user->id}");
        if (!$lockoutTime) {
            return 0;
        }

        $unlockTime = $lockoutTime->addMinutes(self::LOCKOUT_MINUTES);
        return max(0, (int) now()->diffInMinutes($unlockTime, false));
    }

    /**
     * Get remaining resends
     */
    public function getRemainingResends(User $user): int
    {
        $resendCount = Cache::get("otp_resend_count_{$user->id}", 0);
        return max(0, self::MAX_RESENDS - $resendCount);
    }
}s