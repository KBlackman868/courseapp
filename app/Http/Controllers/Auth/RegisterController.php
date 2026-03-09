<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AccountRequest;
use App\Models\SystemNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * RegisterController
 *
 * Handles user registration for both MOH Staff and External Users.
 *
 * REGISTRATION WORKFLOW (same for both):
 * 1. User submits registration form
 * 2. AccountRequest created in "pending" status
 * 3. User sees "Request submitted, pending approval" page
 * 4. Course Admin reviews and approves/rejects
 * 5. On approval: User account created, Moodle account synced, notification sent
 * 6. User can then log in
 *
 * NOTE: Google OAuth has been REMOVED. All authentication is via password.
 */
class RegisterController extends Controller
{

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle registration submission
     *
     * MOH Staff go through account request workflow
     * External users are auto-approved
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => [
                'required', 'string', 'email', 'max:255',
                'unique:users',
                Rule::unique('account_requests', 'email')->where(fn ($query) => $query->where('status', AccountRequest::STATUS_PENDING)),
            ],
            'department' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'date_of_birth' => 'required|date|before_or_equal:' . now()->subYears(18)->toDateString(),
        ]);

        // Determine password minimum based on account type
        // MOH staff (high-risk): 14 chars; External (standard): 12 chars
        $isMoh = User::isMohEmail($validatedData['email']);
        $minLength = $isMoh ? 14 : 12;
        $passwordData = $request->validate([
            'password' => "required|string|min:{$minLength}|confirmed",
        ], [
            'password.min' => "Password must be at least {$minLength} characters for " . ($isMoh ? 'MOH staff (high-risk) accounts' : 'standard accounts') . '.',
            'date_of_birth.before_or_equal' => 'You must be at least 18 years old to register.',
        ]);

        $validatedData = array_merge($validatedData, $passwordData);

        // Check if this is an MOH Staff registration
        if ($isMoh) {
            return $this->handleMohStaffRegistration($request, $validatedData);
        }

        // External user - redirect to external registration page
        // Or handle as auto-approved registration
        return $this->handleExternalRegistration($request, $validatedData);
    }

    /**
     * Handle MOH Staff registration
     *
     * Creates an AccountRequest that needs Course Admin approval
     */
    private function handleMohStaffRegistration(Request $request, array $validatedData)
    {
        // Create account request instead of user
        $accountRequest = AccountRequest::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'department' => $validatedData['department'],
            'organization' => $validatedData['organization'] ?? 'Ministry of Health Trinidad and Tobago',
            'phone' => $validatedData['phone'] ?? null,
            'status' => AccountRequest::STATUS_PENDING,
            'request_type' => AccountRequest::TYPE_MOH_STAFF,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Log::info('MOH Staff account request submitted', [
            'request_id' => $accountRequest->id,
            'email' => $accountRequest->email,
            'department' => $accountRequest->department,
        ]);

        // Log the action (wrapped to prevent cascading failures)
        try {
            ActivityLogger::log(
                'account_request_submitted',
                "MOH Staff account request submitted for {$accountRequest->email}",
                $accountRequest,
                [
                    'email' => $accountRequest->email,
                    'department' => $accountRequest->department,
                ],
                'success',
                'info'
            );
        } catch (\Exception $e) {
            Log::warning('Activity logging failed during MOH registration', [
                'error' => $e->getMessage(),
            ]);
        }

        // Notify Course Admins about new request
        try {
            SystemNotification::notifyNewAccountRequest($accountRequest);
        } catch (\Exception $e) {
            Log::warning('Failed to notify admins about MOH registration', [
                'error' => $e->getMessage(),
            ]);
        }

        // Show registration pending page
        return Inertia::render('Auth/RegistrationPending', [
            'email' => $accountRequest->email,
            'type' => 'moh_staff',
        ]);
    }

    /**
     * Handle External User registration
     *
     * External users go through account request workflow (same as MOH Staff).
     * Account is NOT created until admin approves the request.
     * No Moodle account, OTP, or welcome email until approval.
     */
    private function handleExternalRegistration(Request $request, array $validatedData)
    {
        Log::info('External registration attempt via RegisterController', ['email' => $validatedData['email']]);

        // Create account request instead of user (same pattern as MOH Staff)
        $accountRequest = AccountRequest::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'department' => $validatedData['department'],
            'organization' => $validatedData['organization'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
            'status' => AccountRequest::STATUS_PENDING,
            'request_type' => AccountRequest::TYPE_EXTERNAL,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Log::info('External user account request submitted', [
            'request_id' => $accountRequest->id,
            'email' => $accountRequest->email,
        ]);

        // Log the action (wrapped to prevent cascading failures)
        try {
            ActivityLogger::log(
                'account_request_submitted',
                "External user account request submitted for {$accountRequest->email}",
                $accountRequest,
                [
                    'email' => $accountRequest->email,
                    'organization' => $accountRequest->organization,
                ],
                'success',
                'info'
            );
        } catch (\Exception $e) {
            Log::warning('Activity logging failed during external registration', [
                'error' => $e->getMessage(),
            ]);
        }

        // Notify Course Admins about new request
        try {
            SystemNotification::notifyNewAccountRequest($accountRequest);
        } catch (\Exception $e) {
            Log::warning('Failed to notify admins about external registration', [
                'error' => $e->getMessage(),
            ]);
        }

        // Redirect to registration pending page
        return Inertia::render('Auth/RegistrationPending', [
            'email' => $accountRequest->email,
            'type' => 'external',
        ]);
    }

    /**
     * Show the MOH Staff account request form
     *
     * This is a dedicated form for MOH Staff to request an account.
     * Uses the same UI as external registration but with MOH-specific messaging.
     */
    public function showMohRequestForm()
    {
        return Inertia::render('Auth/MohRequestAccount');
    }

    /**
     * Handle MOH Staff account request submission
     *
     * Same logic as the main register method but dedicated to MOH Staff flow.
     * Creates an AccountRequest that needs Course Admin approval.
     */
    public function submitMohRequest(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                Rule::unique('account_requests', 'email')->where(fn ($query) => $query->where('status', AccountRequest::STATUS_PENDING)),
                // Ensure it's an MOH email
                function ($attribute, $value, $fail) {
                    if (!User::isMohEmail($value)) {
                        $fail('Only @' . User::MOH_EMAIL_DOMAIN . ' email addresses can request MOH Staff accounts.');
                    }
                },
            ],
            'password'   => 'required|string|min:14|confirmed',
            'department' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'date_of_birth' => 'required|date|before_or_equal:' . now()->subYears(18)->toDateString(),
        ], [
            'password.min' => 'Password must be at least 14 characters for MOH staff (high-risk) accounts.',
            'date_of_birth.before_or_equal' => 'You must be at least 18 years old to register.',
        ]);

        // Create account request
        $accountRequest = AccountRequest::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'department' => $validatedData['department'],
            'organization' => 'Ministry of Health Trinidad and Tobago',
            'phone' => $validatedData['phone'] ?? null,
            'status' => AccountRequest::STATUS_PENDING,
            'request_type' => AccountRequest::TYPE_MOH_STAFF,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Log::info('MOH Staff account request submitted via dedicated form', [
            'request_id' => $accountRequest->id,
            'email' => $accountRequest->email,
            'department' => $accountRequest->department,
        ]);

        // Log the action
        ActivityLogger::log(
            'account_request_submitted',
            "MOH Staff account request submitted for {$accountRequest->email}",
            $accountRequest,
            [
                'email' => $accountRequest->email,
                'department' => $accountRequest->department,
            ],
            'success',
            'info'
        );

        // Notify Course Admins about new request
        SystemNotification::notifyNewAccountRequest($accountRequest);

        // Redirect to confirmation page
        return redirect()->route('moh.request-submitted');
    }

    /**
     * Show MOH request submitted confirmation page
     */
    public function mohRequestSubmitted()
    {
        return Inertia::render('Auth/MohRequestSubmitted');
    }
}
