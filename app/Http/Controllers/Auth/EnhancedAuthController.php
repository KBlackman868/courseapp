<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LdapService;
use App\Services\OtpService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class EnhancedAuthController extends Controller
{
    private LdapService $ldapService;
    private OtpService $otpService;

    public function __construct(LdapService $ldapService, OtpService $otpService)
    {
        $this->ldapService = $ldapService;
        $this->otpService = $otpService;
    }

    /**
     * Display the login form
     */
    public function showLoginForm()
    {
        return view('auth.enhanced-login');
    }

    /**
     * Handle login request
     * Determines if user is internal (LDAP) or external (local)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        // Determine if this is an internal or external user based on email domain
        $isInternal = $this->ldapService->isInternalDomain($email);

        if ($isInternal && $this->ldapService->isEnabled()) {
            // Attempt LDAP authentication for internal users
            return $this->handleInternalLogin($request, $email, $password);
        } else {
            // Handle external user authentication
            return $this->handleExternalLogin($request, $email, $password);
        }
    }

    /**
     * Handle internal user login via LDAP
     */
    private function handleInternalLogin(Request $request, string $email, string $password)
    {
        Log::info('Internal user login attempt', ['email' => $email]);

        // Extract username from email for LDAP authentication
        $username = explode('@', $email)[0];

        // Attempt LDAP authentication
        $ldapUser = $this->ldapService->authenticate($username, $password);

        if (!$ldapUser) {
            // Log failed attempt
            ActivityLogger::logAuth('ldap_login_failed', "LDAP authentication failed", [
                'email' => $email,
                'ip_address' => $request->ip(),
            ], 'failed', 'warning');

            return back()->withErrors([
                'email' => 'Invalid credentials or LDAP authentication failed.',
            ])->withInput($request->only('email'));
        }

        // Find or create local user
        $user = $this->findOrCreateInternalUser($ldapUser, $email);

        // Check if user needs OTP verification (one-time only)
        if ($this->otpService->needsOtpVerification($user)) {
            return $this->initiateOtpVerification($user, $request);
        }

        // Login the user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate(); // SECURITY: Prevent session fixation attacks

        // Log successful login
        ActivityLogger::logAuth('ldap_login', "Internal user logged in via LDAP", [
            'user_id' => $user->id,
            'email' => $user->email,
            'user_type' => 'internal',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->intended('/dashboard')
            ->with('success', "Welcome back, {$user->first_name}!");
    }

    /**
     * Handle external user login (local authentication)
     */
    private function handleExternalLogin(Request $request, string $email, string $password)
    {
        Log::info('External user login attempt', ['email' => $email]);

        // Find the user
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Log failed attempt
            ActivityLogger::logAuth('login_failed', "User not found", [
                'email' => $email,
                'ip_address' => $request->ip(),
            ], 'failed', 'warning');

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }

        // Check password
        if (!Hash::check($password, $user->password)) {
            // Log failed attempt
            ActivityLogger::logAuth('login_failed', "Invalid password", [
                'email' => $email,
                'ip_address' => $request->ip(),
            ], 'failed', 'warning');

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }

        // Check if user is suspended
        if ($user->is_suspended) {
            ActivityLogger::logAuth('login_blocked', "Suspended user attempted login", [
                'user_id' => $user->id,
                'email' => $email,
                'ip_address' => $request->ip(),
            ], 'failed', 'warning');

            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact support.',
            ]);
        }

        // Set user_type based on email domain:
        // - health.gov.tt users are always internal (MOH staff), even when using
        //   the email/password login form instead of LDAP
        // - All other users are external
        if (User::isMohEmail($user->email)) {
            if ($user->user_type !== 'internal') {
                $user->update([
                    'user_type' => 'internal',
                    'auth_method' => 'local',
                ]);
            }
        } elseif ($user->user_type !== 'external') {
            $user->update([
                'user_type' => $userType,
                'auth_method' => 'local',
            ]);
        }

        // Check if user needs OTP verification (one-time only)
        if ($this->otpService->needsOtpVerification($user)) {
            return $this->initiateOtpVerification($user, $request);
        }

        // Login the user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate(); // SECURITY: Prevent session fixation attacks

        // Log successful login
        ActivityLogger::logAuth('login', "External user logged in", [
            'user_id' => $user->id,
            'email' => $user->email,
            'user_type' => 'external',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->intended('/dashboard')
            ->with('success', "Welcome back, {$user->first_name}!");
    }

    /**
     * Find or create internal user from LDAP data
     */
    private function findOrCreateInternalUser(array $ldapUser, string $email): User
    {
        // Try to find by LDAP GUID first
        if (!empty($ldapUser['ldap_guid'])) {
            $user = User::where('ldap_guid', $ldapUser['ldap_guid'])->first();
            if ($user) {
                // Update user data from LDAP
                $user->update([
                    'first_name' => $ldapUser['first_name'] ?? $user->first_name,
                    'last_name' => $ldapUser['last_name'] ?? $user->last_name,
                    'department' => $ldapUser['department'] ?? $user->department,
                    'ldap_synced_at' => now(),
                ]);
                return $user;
            }
        }

        // Try to find by email
        $user = User::where('email', $email)->first();

        if ($user) {
            // Link existing user to LDAP
            $user->update([
                'ldap_guid' => $ldapUser['ldap_guid'],
                'ldap_username' => $ldapUser['ldap_username'],
                'user_type' => 'internal',
                'auth_method' => 'ldap',
                'first_name' => $ldapUser['first_name'] ?? $user->first_name,
                'last_name' => $ldapUser['last_name'] ?? $user->last_name,
                'department' => $ldapUser['department'] ?? $user->department,
                'ldap_synced_at' => now(),
            ]);

            // Check if user should be a course creator
            if (isset($ldapUser['dn'])) {
                $isCourseCreator = $this->ldapService->isCourseCreator($ldapUser['dn']);
                $user->update(['is_course_creator' => $isCourseCreator]);
            }

            return $user;
        }

        // Create new internal user
        $user = User::create([
            'first_name' => $ldapUser['first_name'] ?? 'Unknown',
            'last_name' => $ldapUser['last_name'] ?? 'User',
            'email' => $email,
            'password' => Hash::make(\Str::random(32)), // Random password for LDAP users
            'department' => $ldapUser['department'] ?? 'Ministry of Health',
            'organization' => 'Ministry of Health Trinidad and Tobago',
            'ldap_guid' => $ldapUser['ldap_guid'],
            'ldap_username' => $ldapUser['ldap_username'],
            'user_type' => 'internal',
            'auth_method' => 'ldap',
            'email_verified_at' => now(), // LDAP users are pre-verified
            'verification_status' => 'verified',
            'ldap_synced_at' => now(),
        ]);

        // Assign MOH staff role for internal LDAP users
        $user->assignRole(User::ROLE_MOH_STAFF);

        // Check if user should be a course creator
        if (isset($ldapUser['dn'])) {
            $isCourseCreator = $this->ldapService->isCourseCreator($ldapUser['dn']);
            $user->update(['is_course_creator' => $isCourseCreator]);
        }

        Log::info('Created new internal user from LDAP', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $user;
    }

    /**
     * Initiate OTP verification process
     */
    private function initiateOtpVerification(User $user, Request $request)
    {
        // Store user ID in session for OTP verification
        Session::put('otp_user_id', $user->id);
        Session::put('otp_remember', $request->boolean('remember'));

        // Send OTP
        if (!$this->otpService->sendOtp($user)) {
            return back()->withErrors([
                'email' => 'Failed to send verification code. Please try again.',
            ]);
        }

        // Log OTP sent
        ActivityLogger::logAuth('otp_sent', "OTP verification initiated", [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('auth.otp.verify')
            ->with('info', 'A verification code has been sent to your email.');
    }

    /**
     * Show OTP verification form
     */
    public function showOtpForm()
    {
        // Check if user is in OTP verification flow
        if (!Session::has('otp_user_id')) {
            return redirect()->route('login')
                ->with('error', 'Please login first.');
        }

        $user = User::find(Session::get('otp_user_id'));

        if (!$user) {
            Session::forget(['otp_user_id', 'otp_remember']);
            return redirect()->route('login')
                ->with('error', 'Session expired. Please login again.');
        }

        return view('auth.otp-verify', [
            'email' => $user->email,
            'maskedEmail' => $this->maskEmail($user->email),
        ]);
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        // Get user from session
        $userId = Session::get('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Session expired. Please login again.');
        }

        $user = User::find($userId);

        if (!$user) {
            Session::forget(['otp_user_id', 'otp_remember']);
            return redirect()->route('login')
                ->with('error', 'User not found. Please login again.');
        }

        // Check if locked out
        if ($this->otpService->isLockedOut($user)) {
            $minutes = $this->otpService->getLockoutRemainingMinutes($user);
            return back()->withErrors([
                'otp_code' => "Too many failed attempts. Please try again in {$minutes} minutes.",
            ]);
        }

        // Verify the OTP
        $code = $request->input('otp_code');

        if (!$this->otpService->verifyOtp($user, $code)) {
            // Log failed attempt
            ActivityLogger::logAuth('otp_failed', "OTP verification failed", [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
            ], 'failed', 'warning');

            return back()->withErrors([
                'otp_code' => 'Invalid or expired verification code.',
            ]);
        }

        // Clear session data but preserve CSRF token
        $remember = Session::get('otp_remember', false);
        Session::forget(['otp_user_id', 'otp_remember']);

        // Login the user
        Auth::login($user, $remember);
        request()->session()->regenerate(); // SECURITY: Prevent session fixation attacks

        // Log successful verification
        ActivityLogger::logAuth('otp_verified', "OTP verification successful - initial login completed", [
            'user_id' => $user->id,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->intended('/dashboard')
            ->with('success', "Welcome, {$user->first_name}! Your account has been verified.");
    }

    /**
     * Resend OTP code
     */
    public function resendOtp(Request $request)
    {
        $userId = Session::get('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Session expired. Please login again.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User not found. Please login again.');
        }

        try {
            $this->otpService->resendOtp($user);

            // Log resend
            ActivityLogger::logAuth('otp_resent', "OTP code resent", [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
            ]);

            return back()->with('success', 'A new verification code has been sent to your email.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'otp_code' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mask email for display
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 4)) . substr($name, -2);

        return $maskedName . '@' . $domain;
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLogger::logAuth('logout', "User logged out", [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
