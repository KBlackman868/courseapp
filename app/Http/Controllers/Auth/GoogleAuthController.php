<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;

class GoogleAuthController extends Controller
{
    /**
     * Internal MOH email domains - users with these domains get automatic course access
     */
    private const INTERNAL_DOMAINS = [
        'health.gov.tt',
        'moh.gov.tt',
    ];

    /**
     * Redirect to Google OAuth
     * This single method handles both login and registration
     * Now accepts all Google accounts (internal MOH and external users)
     */
    public function redirectToGoogle()
    {
        // Don't restrict to specific domain - allow all Google accounts
        return Socialite::driver('google')->redirect();
    }

    /**
     * Alternative: Explicit registration route (optional)
     */
    public function redirectToGoogleForRegister()
    {
        session(['google_auth_intent' => 'register']);
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     * SMART DETECTION: Automatically creates account if user doesn't exist
     * Users with @health.gov.tt are marked as internal (direct course access)
     * All other users are marked as external (require approval for courses)
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            Log::info('Google OAuth callback initiated');

            // ===== SSL CERTIFICATE CONFIGURATION =====
            $certPath = storage_path('certs/cacert.pem');

            if (file_exists($certPath) && is_readable($certPath)) {
                $httpClient = new \GuzzleHttp\Client([
                    'verify' => $certPath,
                    'timeout' => 30,
                ]);
            } else {
                Log::warning('CA certificate not found - using insecure connection');
                $httpClient = new \GuzzleHttp\Client([
                    'verify' => false,
                    'timeout' => 30,
                ]);
            }

            // Get Google user data
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->setHttpClient($httpClient)
                ->user();

            // ===== DETERMINE USER TYPE BASED ON EMAIL DOMAIN =====
            $email = $googleUser->getEmail();
            $emailDomain = $this->getEmailDomain($email);
            $isInternal = $this->isInternalDomain($emailDomain);

            Log::info('Google user authenticated', [
                'email' => $email,
                'domain' => $emailDomain,
                'user_type' => $isInternal ? 'internal' : 'external',
            ]);
            
            // ===== EXTRACT PROPER NAMES =====
            $googleUserArray = $googleUser->user;
            $firstName = $googleUserArray['given_name'] ?? null;
            $lastName = $googleUserArray['family_name'] ?? null;

            if (empty($firstName) || empty($lastName)) {
                $nameParts = $this->parseFullName($googleUser->getName());
                $firstName = $firstName ?: $nameParts['first_name'];
                $lastName = $lastName ?: $nameParts['last_name'];
            }

            // Never use TEST or generic names
            if (strtoupper($firstName) === 'TEST' || empty(trim($firstName))) {
                $emailParts = explode('@', $email);
                $emailName = str_replace('.', ' ', $emailParts[0]);
                $emailNameParts = explode(' ', $emailName);
                $firstName = ucfirst($emailNameParts[0]);
                $lastName = isset($emailNameParts[1]) ? ucfirst($emailNameParts[1]) : '';
            }

            // ===== SMART USER HANDLING =====
            // Check if user exists
            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                // USER EXISTS - LOG THEM IN
                return $this->loginExistingUser($existingUser, $googleUser, $firstName, $lastName, $isInternal);
            } else {
                // USER DOESN'T EXIST - CREATE NEW ACCOUNT
                // Create account for any Google user (internal MOH or external)
                return $this->createNewUser($googleUser, $firstName, $lastName, $isInternal);
            }
            
        } catch (\Exception $e) {
            Log::error('Google OAuth Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Log the error
            ActivityLogger::logAuth('google_oauth_error', "Google OAuth authentication failed", [
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], 'failed', 'error');
            
            return redirect('/login')->with('error', 
                'Unable to authenticate with Google. Please try again.'
            );
        }
    }
    
    /**
     * Create a new user account
     * Internal MOH users get direct course access
     * External users require approval for course enrollment
     */
    private function createNewUser($googleUser, $firstName, $lastName, bool $isInternal)
    {
        $email = $googleUser->getEmail();
        $userType = $isInternal ? User::TYPE_INTERNAL : User::TYPE_EXTERNAL;

        Log::info('Creating new user account', [
            'email' => $email,
            'name' => "$firstName $lastName",
            'user_type' => $userType,
        ]);

        // Determine organization based on user type
        $organization = $isInternal
            ? 'Ministry of Health Trinidad and Tobago'
            : $this->inferOrganizationFromEmail($email);

        // Create the user
        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'google_id' => $googleUser->getId(),
            'profile_photo' => $googleUser->getAvatar(),
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(24)),
            'department' => $isInternal ? $this->inferDepartment($email) : null,
            'organization' => $organization,
            'verification_status' => 'verified',
            'verification_sent_at' => now(),
            'verification_attempts' => 0,
            'must_verify_before' => null,
            'user_type' => $userType,
            'auth_method' => 'google',
        ]);

        // Assign default role
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('user');
        }

        Log::info('New user created successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'user_type' => $userType,
        ]);

        // Log the registration
        ActivityLogger::logAuth('google_register', "New user registered via Google OAuth", [
            'user_id' => $user->id,
            'email' => $user->email,
            'domain' => $this->getEmailDomain($user->email),
            'user_type' => $userType,
            'organization' => $organization,
            'google_id' => $googleUser->getId(),
            'ip_address' => request()->ip()
        ]);

        // Fire registration event
        event(new Registered($user));

        // Create Moodle account for internal users immediately
        // External users will get Moodle account when their enrollment is approved
        if ($isInternal && class_exists(CreateOrLinkMoodleUser::class)) {
            try {
                CreateOrLinkMoodleUser::dispatch(
                    $user,
                    $user->email,
                    $firstName,
                    $lastName
                );
                Log::info('Moodle user creation job dispatched for internal user');

                ActivityLogger::logMoodle('user_sync_dispatched',
                    "Moodle user creation job dispatched for new internal user",
                    $user,
                    ['email' => $user->email]
                );
            } catch (\Exception $e) {
                Log::error('Failed to dispatch Moodle job', ['error' => $e->getMessage()]);

                ActivityLogger::logMoodle('user_sync_failed',
                    "Failed to dispatch Moodle user creation",
                    $user,
                    ['error' => $e->getMessage()],
                    'failed',
                    'error'
                );
            }
        }

        // Log the user in
        Auth::login($user, true);

        // Welcome message based on user type
        if ($isInternal) {
            return redirect('/mycourses')->with('success',
                "Welcome to the Ministry of Health Learning Platform, $firstName! " .
                "Your account has been created successfully. " .
                "As MOH staff, you have direct access to all courses."
            );
        } else {
            return redirect('/dashboard')->with('success',
                "Welcome to the Ministry of Health Learning Platform, $firstName! " .
                "Your account has been created successfully. " .
                "As an external user, course enrollment requires administrator approval."
            );
        }
    }
    
    /**
     * Login an existing user
     */
    private function loginExistingUser($user, $googleUser, $firstName, $lastName, bool $isInternal)
    {
        // Check if suspended
        if ($user->is_suspended ?? false) {
            Log::warning('Suspended user login attempt', ['user_id' => $user->id]);

            ActivityLogger::logAuth('login_blocked', "Suspended user attempted login", [
                'user_id' => $user->id,
                'email' => $user->email,
                'reason' => 'suspended',
                'ip_address' => request()->ip()
            ], 'failed', 'warning');

            return redirect('/login')->with('error',
                'Your account has been suspended. Please contact IT support.'
            );
        }

        // Update user data if needed
        $updates = [];

        // Link Google account if not linked
        if (!$user->google_id) {
            $updates['google_id'] = $googleUser->getId();
            $updates['profile_photo'] = $user->profile_photo ?: $googleUser->getAvatar();
        }

        // Update user type if not set or if it changed
        $expectedUserType = $isInternal ? User::TYPE_INTERNAL : User::TYPE_EXTERNAL;
        if (empty($user->user_type) || $user->user_type !== $expectedUserType) {
            $updates['user_type'] = $expectedUserType;
            Log::info('Updating user type', [
                'user_id' => $user->id,
                'old_type' => $user->user_type,
                'new_type' => $expectedUserType
            ]);
        }

        // Update auth method if not set
        if (empty($user->auth_method)) {
            $updates['auth_method'] = 'google';
        }

        // Fix TEST USER names
        if (strtoupper($user->first_name) === 'TEST' ||
            empty(trim($user->first_name)) ||
            $user->first_name === 'User') {
            $updates['first_name'] = $firstName;
            $updates['last_name'] = $lastName;
            Log::info('Updating generic name for user', [
                'user_id' => $user->id,
                'new_name' => "$firstName $lastName"
            ]);
        }

        // Verify email if not verified
        if (!$user->email_verified_at) {
            $updates['email_verified_at'] = now();
            $updates['verification_status'] = 'verified';
        }

        // Apply updates if any
        if (!empty($updates)) {
            $user->update($updates);
        }

        // Log the user in
        Auth::login($user, true);

        Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'user_type' => $user->user_type,
        ]);

        // Log successful login
        $emailDomain = $this->getEmailDomain($user->email);
        ActivityLogger::logAuth('google_login', "User logged in via Google OAuth", [
            'user_id' => $user->id,
            'email' => $user->email,
            'domain' => $emailDomain,
            'user_type' => $user->user_type,
            'google_id' => $googleUser->getId(),
            'ip_address' => request()->ip(),
            'profile_updated' => !empty($updates)
        ]);

        // Redirect internal users to My Courses, external users to dashboard
        if ($isInternal) {
            return redirect()->intended('/mycourses')->with('success',
                "Welcome back, $firstName!"
            );
        } else {
            return redirect()->intended('/dashboard')->with('success',
                "Welcome back, $firstName!"
            );
        }
    }
    
    /**
     * Parse full name into first and last name
     */
    private function parseFullName($fullName)
    {
        $fullName = trim($fullName);
        $fullName = preg_replace('/^test\s+/i', '', $fullName);
        
        $nameParts = explode(' ', $fullName);
        
        if (count($nameParts) == 1) {
            return [
                'first_name' => ucfirst($nameParts[0]),
                'last_name' => '',
            ];
        } elseif (count($nameParts) == 2) {
            return [
                'first_name' => ucfirst($nameParts[0]),
                'last_name' => ucfirst($nameParts[1]),
            ];
        } else {
            $firstName = ucfirst(array_shift($nameParts));
            $lastName = implode(' ', array_map('ucfirst', $nameParts));
            return [
                'first_name' => $firstName,
                'last_name' => $lastName,
            ];
        }
    }
    
    /**
     * Get domain from email address
     */
    private function getEmailDomain($email)
    {
        $parts = explode('@', $email);
        return strtolower($parts[1] ?? '');
    }
    
    /**
     * Check if email domain is an internal MOH domain
     */
    private function isInternalDomain($domain)
    {
        return in_array(strtolower($domain), array_map('strtolower', self::INTERNAL_DOMAINS));
    }

    /**
     * Try to infer department from email (for internal users)
     */
    private function inferDepartment($email)
    {
        $emailParts = explode('@', $email);
        $localPart = $emailParts[0] ?? '';

        $departments = [
            'cardiology' => 'Cardiology',
            'emergency' => 'Emergency Medicine',
            'pediatrics' => 'Pediatrics',
            'surgery' => 'Surgery',
            'nursing' => 'Nursing',
            'pharmacy' => 'Pharmacy',
            'lab' => 'Laboratory Services',
            'admin' => 'Administration',
            'it' => 'Information Technology',
            'hr' => 'Human Resources',
            'finance' => 'Finance',
        ];

        foreach ($departments as $keyword => $department) {
            if (stripos($localPart, $keyword) !== false) {
                return $department;
            }
        }

        return 'Ministry of Health';
    }

    /**
     * Try to infer organization from email domain (for external users)
     */
    private function inferOrganizationFromEmail($email)
    {
        $domain = $this->getEmailDomain($email);

        // Common email providers - return generic organization
        $genericProviders = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'live.com', 'icloud.com'];
        if (in_array($domain, $genericProviders)) {
            return 'External Organization';
        }

        // Try to extract organization name from domain
        $domainParts = explode('.', $domain);
        if (count($domainParts) >= 2) {
            // Get the main part of the domain (e.g., "company" from "company.com")
            return ucfirst($domainParts[0]);
        }

        return 'External Organization';
    }
}