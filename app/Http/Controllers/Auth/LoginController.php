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

class LoginController extends Controller
{
    protected $redirectTo = '/home';
    
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
        return view('pages.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = $request->email;
        $password = $request->password;

        // Use local password authentication for all users
        return $this->handleExternalLogin($request, $email, $password);
    }

    /**
     * Handle internal (MOH) user login via LDAP
     */
    private function handleInternalLogin(Request $request, string $email, string $password)
    {
        // Extract username from email for LDAP lookup
        $username = explode('@', $email)[0];

        // Authenticate against LDAP
        $ldapData = $this->ldapService->authenticate($username, $password);

        if (!$ldapData) {
            // Try with full email
            $ldapData = $this->ldapService->authenticate($email, $password);
        }

        if (!$ldapData) {
            Log::warning('LDAP authentication failed', ['email' => $email]);
            
            if (class_exists(ActivityLogger::class)) {
                ActivityLogger::logAuth('ldap_login_failed', 'LDAP authentication failed', [
                    'email' => $email,
                    'ip_address' => $request->ip()
                ], 'failed', 'warning');
            }

            return back()->withErrors([
                'email' => 'Invalid credentials. Please check your MOH username and password.',
            ])->withInput($request->only('email'));
        }

        // Find or create user from LDAP data
        $user = $this->ldapService->findOrCreateUser($ldapData);

        if (!$user) {
            return back()->withErrors([
                'email' => 'Unable to process your account. Please contact IT support.',
            ])->withInput($request->only('email'));
        }

        // Check if user needs OTP verification
        if ($this->otpService->needsOtpVerification($user)) {
            return $this->initiateOtpVerification($user);
        }

        // Log the user in
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if (class_exists(ActivityLogger::class)) {
            ActivityLogger::logAuth('ldap_login', 'User logged in via LDAP', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);
        }

        return redirect()->intended($this->redirectTo)
            ->with('success', "Welcome back, {$user->first_name}!");
    }

    /**
     * Handle external user login (standard password auth)
     */
    private function handleExternalLogin(Request $request, string $email, string $password)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            if (class_exists(ActivityLogger::class)) {
                ActivityLogger::logAuth('login_failed', 'Login failed - invalid credentials', [
                    'email' => $email,
                    'ip_address' => $request->ip()
                ], 'failed', 'warning');
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }

        // Check if suspended
        if ($user->is_suspended ?? false) {
            if (class_exists(ActivityLogger::class)) {
                ActivityLogger::logAuth('login_blocked', 'Suspended user attempted login', [
                    'user_id' => $user->id,
                    'email' => $email,
                    'ip_address' => $request->ip()
                ], 'failed', 'warning');
            }

            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact support.',
            ])->withInput($request->only('email'));
        }

        // Mark as external user if not already set
        if (!$user->user_type || $user->user_type !== 'external') {
            $user->update(['user_type' => 'external']);
        }

        // Check if user needs OTP verification
        if ($this->otpService->needsOtpVerification($user)) {
            return $this->initiateOtpVerification($user);
        }

        // Log the user in
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if (class_exists(ActivityLogger::class)) {
            ActivityLogger::logAuth('login', 'User logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);
        }

        return redirect()->intended($this->redirectTo)
            ->with('success', "Welcome back, {$user->first_name}!");
    }

    /**
     * Initiate OTP verification process
     */
    private function initiateOtpVerification(User $user)
    {
        $result = $this->otpService->sendOtp($user);

        if (!$result['success']) {
            return back()->withErrors(['email' => $result['message']]);
        }

        // Store user ID in session for OTP verification
        session(['otp_user_id' => $user->id]);

        return redirect()->route('auth.otp.verify')
            ->with('info', 'Please enter the verification code sent to your email.');
    }

    /**
     * Show OTP verification form
     */
    public function showOtpForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('otp_user_id'));

        if (!$user) {
            session()->forget('otp_user_id');
            return redirect()->route('login');
        }

        $remainingResends = $this->otpService->getRemainingResends($user);

        return view('auth.otp-verify', compact('user', 'remainingResends'));
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);

        if (!$user) {
            session()->forget('otp_user_id');
            return redirect()->route('login');
        }

        $result = $this->otpService->verifyOtp($user, $request->otp);

        if (!$result['success']) {
            return back()->withErrors(['otp' => $result['message']]);
        }

        // CRITICAL: Refresh the user model to get updated verification status
        $user->refresh();

        // Check if this is a new registration - also verify email
        $isNewRegistration = session('registration_pending', false);
        if ($isNewRegistration) {
            $user->update([
                'email_verified_at' => now(),
                'verification_status' => 'verified',
            ]);

            Log::info('New user email verified via OTP', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        // Clear session
        session()->forget('otp_user_id');

        // Log the user in
        Auth::login($user, true);
        $request->session()->regenerate();

        if (class_exists(ActivityLogger::class)) {
            ActivityLogger::logAuth('otp_verified', 'User completed OTP verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);
        }

        // For new registrations, send welcome email and trigger Moodle sync
        if ($isNewRegistration) {
            try {
                // Send welcome email with credentials
                $tempPassword = \Illuminate\Support\Facades\Cache::get('moodle_temp_password_' . $user->id);
                if ($tempPassword && class_exists(\App\Mail\WelcomeEmail::class)) {
                    \Illuminate\Support\Facades\Mail::to($user->email)
                        ->send(new \App\Mail\WelcomeEmail($user, $tempPassword));
                }

                // Queue Moodle user creation
                if (class_exists(\App\Jobs\CreateOrLinkMoodleUser::class)) {
                    \App\Jobs\CreateOrLinkMoodleUser::dispatch($user);
                }
            } catch (\Exception $e) {
                Log::warning('Post-verification tasks failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->intended($this->redirectTo)
                ->with('success', "Welcome, {$user->first_name}! Your email has been verified and your account is now active.");
        }

        $welcomeMessage = $isNewRegistration
            ? "Welcome, {$user->first_name}! Your account has been created and verified."
            : "Welcome back, {$user->first_name}! Your account has been verified.";

        return redirect()->intended($this->redirectTo)
            ->with('success', "Welcome, {$user->first_name}! Your account has been verified.");
    }

    /**
     * Resend OTP code
     */
    public function resendOtp(Request $request)
    {
        $userId = session('otp_user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Session expired.'], 400);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $result = $this->otpService->sendOtp($user);

        return response()->json($result);
    }

    /**
     * Logout the user
     *
     * IMPORTANT: This method handles the logout flow properly to avoid
     * "Page Expired" (419) errors. The key is to:
     * 1. Log the action BEFORE invalidating the session
     * 2. Invalidate the session
     * 3. Regenerate the CSRF token
     * 4. Return a redirect (not an Inertia response)
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log the action BEFORE invalidating the session
        if ($user && class_exists(ActivityLogger::class)) {
            ActivityLogger::logAuth('logout', 'User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);
        }

        // Clear any OTP-related session data
        $request->session()->forget('otp_user_id');
        $request->session()->forget('registration_pending');

        // Logout the user
        Auth::logout();

        // Invalidate the session and regenerate the CSRF token
        // This is critical to prevent "Page Expired" errors
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Use a standard redirect (not Inertia) to avoid CSRF issues
        // The redirect will load a fresh page with a new CSRF token
        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }
}
