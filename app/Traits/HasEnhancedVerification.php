<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HasEnhancedVerification
{
    /**
     * Check if user's email is verified (alias for Laravel's method)
     */
    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }
    
    /**
     * Check if user is pending verification
     */
    public function isPendingVerification(): bool
    {
        return $this->email_verified_at === null 
            && $this->verification_status === 'pending';
    }
    
    /**
     * Check if verification has expired
     */
    public function isVerificationExpired(): bool
    {
        if ($this->must_verify_before === null) {
            return false;
        }
        
        return Carbon::now()->isAfter($this->must_verify_before);
    }
    
    /**
     * Mark email as verified with additional tracking
     */
    public function markEmailAsVerifiedWithTracking(): bool
    {
        if ($this->hasVerifiedEmail()) {
            return true;
        }
        
        $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'verification_status' => 'verified',
        ])->save();
        
        Log::info('User email verified', [
            'user_id' => $this->id,
            'email' => $this->email,
            'verified_at' => now()
        ]);
        
        // Fire the verified event
        event(new \Illuminate\Auth\Events\Verified($this));
        
        return true;
    }
    
    /**
     * Send verification email with tracking
     */
    public function sendVerificationWithTracking(): void
    {
        $this->increment('verification_attempts');
        
        $this->forceFill([
            'verification_status' => 'pending',
            'verification_sent_at' => now(),
            'must_verify_before' => now()->addHours(48), // 48 hour deadline
        ])->save();
        
        $this->sendEmailVerificationNotification();
        
        Log::info('Verification email sent', [
            'user_id' => $this->id,
            'email' => $this->email,
            'attempt' => $this->verification_attempts,
            'expires_at' => $this->must_verify_before
        ]);
    }
    
    /**
     * Get verification status label for display
     */
    public function getVerificationStatusLabelAttribute(): string
    {
        if ($this->hasVerifiedEmail()) {
            return 'Verified';
        }
        
        if ($this->isVerificationExpired()) {
            return 'Expired';
        }
        
        if ($this->verification_sent_at) {
            return 'Pending';
        }
        
        return 'Unverified';
    }
    
    /**
     * Get verification badge color for UI
     */
    public function getVerificationBadgeColorAttribute(): string
    {
        return match($this->verification_status ?? 'unverified') {
            'verified' => 'green',
            'pending' => 'yellow',
            'expired' => 'red',
            default => 'gray'
        };
    }
    
    /**
     * Get verification icon for UI
     */
    public function getVerificationIconAttribute(): string
    {
        return match($this->verification_status ?? 'unverified') {
            'verified' => '✅',
            'pending' => '⏳',
            'expired' => '❌',
            default => '⚠️'
        };
    }
    
    /**
     * Check if user can request new verification email
     */
    public function canRequestVerification(): bool
    {
        // Can't request if already verified
        if ($this->hasVerifiedEmail()) {
            return false;
        }
        
        // Rate limiting: Can only request once per minute
        if ($this->verification_sent_at) {
            $lastSent = Carbon::parse($this->verification_sent_at);
            if ($lastSent->diffInSeconds(now()) < 60) {
                return false;
            }
        }
        
        // Limit total attempts to 10
        if ($this->verification_attempts >= 10) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get seconds until can request new verification
     */
    public function getSecondsUntilCanRequestAttribute(): int
    {
        if (!$this->verification_sent_at) {
            return 0;
        }
        
        $lastSent = Carbon::parse($this->verification_sent_at);
        $secondsPassed = $lastSent->diffInSeconds(now());
        
        if ($secondsPassed >= 60) {
            return 0;
        }
        
        return 60 - $secondsPassed;
    }
    
    /**
     * Get time remaining for verification
     */
    public function getVerificationTimeRemainingAttribute(): ?string
    {
        if (!$this->must_verify_before || $this->hasVerifiedEmail()) {
            return null;
        }
        
        $deadline = Carbon::parse($this->must_verify_before);
        
        if ($deadline->isPast()) {
            return 'Expired';
        }
        
        return $deadline->diffForHumans();
    }
    
    /**
     * Get formatted verification display with icon
     */
    public function getVerificationDisplayAttribute(): string
    {
        return $this->verification_icon . ' ' . $this->verification_status_label;
    }
}