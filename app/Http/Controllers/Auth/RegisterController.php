<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AccountRequest;
use App\Models\SystemNotification;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Mail\WelcomeEmail;
use App\Services\OtpService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

/**
 * RegisterController
 *
 * Handles user registration for both MOH Staff and External Users.
 *
 * REGISTRATION WORKFLOW:
 *
 * MOH STAFF (@health.gov.tt):
 * 1. User submits registration form
 * 2. Account Request created in "pending" status
 * 3. User sees "Request submitted, pending approval" message
 * 4. Course Admin reviews and approves/rejects
 * 5. On approval: User account created, notification sent
 * 6. User can then log in
 *
 * EXTERNAL USERS (other emails):
 * 1. User submits registration form
 * 2. Account created immediately (auto-approved)
 * 3. User goes through OTP verification
 * 4. User can access courses and request enrollment
 *
 * NOTE: Google OAuth has been REMOVED. All authentication is via password.
 */
class RegisterController extends Controller
{
    protected $redirectTo = '/auth/otp/verify';
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('pages.home_register');
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
            'email'      => 'required|string|email|max:255|unique:users|unique:account_requests,email',
            'password'   => 'required|string|min:8|confirmed',
            'department' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        // Check if this is an MOH Staff registration
        if (User::isMohEmail($validatedData['email'])) {
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

        // Redirect with success message
        return redirect()->route('login')->with('success',
            'Your account request has been submitted successfully. ' .
            'A Course Administrator will review your request. ' .
            'You will receive an email notification once your account is approved.'
        );
    }

    /**
     * Handle External User registration
     *
     * External users are auto-approved and go through OTP verification
     */
    private function handleExternalRegistration(Request $request, array $validatedData)
    {
        // Create the user with verification tracking
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name'  => $validatedData['last_name'],
            'email'      => $validatedData['email'],
            'password'   => Hash::make($validatedData['password']),
            'department' => $validatedData['department'],
            'organization' => $validatedData['organization'] ?? null,
            'temp_moodle_password' => encrypt($validatedData['password']),
            'verification_status' => 'pending',
            'verification_sent_at' => now(),
            'verification_attempts' => 1,
            'must_verify_before' => now()->addHours(48),
            'initial_otp_completed' => false,
            'user_type' => User::TYPE_EXTERNAL,
            'account_status' => User::STATUS_ACTIVE, // External users are auto-approved
            'auth_method' => 'local',
        ]);

        // Assign External User role
        $user->assignRole(User::ROLE_EXTERNAL_USER);

        // Store the plain password temporarily for Moodle sync
        Cache::put('moodle_temp_password_' . $user->id, $validatedData['password'], 300);

        Log::info('External user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'verification_status' => 'pending',
        ]);

        // Log the action
        ActivityLogger::logAuth('external_register', "External user registered: {$user->email}", [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Send OTP for verification
        $otpResult = $this->otpService->sendOtp($user);

        if (!$otpResult['success']) {
            Log::error('Failed to send OTP during registration', [
                'user_id' => $user->id,
                'error' => $otpResult['message']
            ]);
        }

        // Store user ID in session for OTP verification (don't log them in yet)
        session(['otp_user_id' => $user->id]);
        session(['registration_pending' => true]);

        // Send welcome email with credentials (queued)
        if (class_exists(WelcomeEmail::class)) {
            Mail::to($user->email)->queue(new WelcomeEmail($user, $validatedData['password']));
        }

        // Redirect to OTP verification page
        return redirect()->route('auth.otp.verify')
            ->with('success', 'Registration successful! Please enter the verification code sent to your email.');
    }

    /**
     * Show the MOH Staff account request form
     *
     * This is a dedicated form for MOH Staff to request an account.
     * Uses the same UI as external registration but with MOH-specific messaging.
     */
    public function showMohRequestForm()
    {
        return view('auth.moh-request-account');
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
                'unique:account_requests,email',
                // Ensure it's an MOH email
                function ($attribute, $value, $fail) {
                    if (!User::isMohEmail($value)) {
                        $fail('Only @' . User::MOH_EMAIL_DOMAIN . ' email addresses can request MOH Staff accounts.');
                    }
                },
            ],
            'password'   => 'required|string|min:8|confirmed',
            'department' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
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
        return view('auth.moh-request-submitted');
    }
}
