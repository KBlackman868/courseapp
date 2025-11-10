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
     * Allowed email domains for Ministry of Health
     */
    private const ALLOWED_DOMAINS = [
        'health.gov.tt',
        'moh.gov.tt',
    ];

    /**
     * Redirect to Google OAuth
     * This single method handles both login and registration
     */
    public function redirectToGoogle()
    {
        // Don't set intent - we'll auto-detect in callback
        return Socialite::driver('google')
            ->with(['hd' => 'health.gov.tt'])  // Hint domain
            ->redirect();
    }
    
    /**
     * Alternative: Explicit registration route (optional)
     */
    public function redirectToGoogleForRegister()
    {
        session(['google_auth_intent' => 'register']);
        return Socialite::driver('google')
            ->with(['hd' => 'health.gov.tt'])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     * SMART DETECTION: Automatically creates account if user doesn't exist
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
            
            // ===== DOMAIN RESTRICTION CHECK =====
            $email = $googleUser->getEmail();
            $emailDomain = $this->getEmailDomain($email);
            
            // Check if email domain is allowed
            if (!$this->isDomainAllowed($emailDomain)) {
                Log::warning('Unauthorized domain attempt', [
                    'email' => $email,
                    'domain' => $emailDomain,
                ]);
                
                // Log failed attempt
                ActivityLogger::logAuth('google_login_blocked', "Unauthorized domain login attempt", [
                    'email' => $email,
                    'domain' => $emailDomain,
                    'ip_address' => $request->ip()
                ], 'failed', 'warning');
                
                return redirect('/login')->with('error', 
                    'Access restricted to Ministry of Health email accounts only. ' .
                    'Please use your @health.gov.tt or @moh.gov.tt email address.'
                );
            }
            
            Log::info('Authorized MOH user authenticated', [
                'email' => $email,
                'domain' => $emailDomain,
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
                return $this->loginExistingUser($existingUser, $googleUser, $firstName, $lastName);
            } else {
                // USER DOESN'T EXIST - CREATE NEW ACCOUNT
                // This is the KEY CHANGE - we automatically create an account for valid MOH emails
                return $this->createNewUser($googleUser, $firstName, $lastName);
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
     * Create a new user account for MOH staff
     */
    private function createNewUser($googleUser, $firstName, $lastName)
    {
        Log::info('Creating new MOH user account', [
            'email' => $googleUser->getEmail(),
            'name' => "$firstName $lastName"
        ]);
        
        // Create the user
        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'profile_photo' => $googleUser->getAvatar(),
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(24)),
            'department' => $this->inferDepartment($googleUser->getEmail()),
            'organization' => 'Ministry of Health Trinidad and Tobago',
            'verification_status' => 'verified',
            'verification_sent_at' => now(),
            'verification_attempts' => 0,
            'must_verify_before' => null,
        ]);
        
        // Assign default role
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('user');
        }
        
        Log::info('New MOH user created successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
        
        // Log the registration
        ActivityLogger::logAuth('google_register', "New MOH user registered via Google OAuth", [
            'user_id' => $user->id,
            'email' => $user->email,
            'domain' => $this->getEmailDomain($user->email),
            'organization' => 'Ministry of Health Trinidad and Tobago',
            'google_id' => $googleUser->getId(),
            'ip_address' => request()->ip()
        ]);
        
        // Fire registration event
        event(new Registered($user));
        
        // Create Moodle account if configured
        if (class_exists(CreateOrLinkMoodleUser::class)) {
            try {
                CreateOrLinkMoodleUser::dispatch(
                    $user,
                    $user->email,
                    $firstName,
                    $lastName
                );
                Log::info('Moodle user creation job dispatched');
                
                // Log Moodle sync attempt
                ActivityLogger::logMoodle('user_sync_dispatched', 
                    "Moodle user creation job dispatched for new user", 
                    $user,
                    ['email' => $user->email]
                );
            } catch (\Exception $e) {
                Log::error('Failed to dispatch Moodle job', ['error' => $e->getMessage()]);
                
                // Log Moodle sync failure
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
        
        // Welcome message for new users
        return redirect('/dashboard')->with('success', 
            "Welcome to the Ministry of Health Learning Platform, $firstName! " .
            "Your account has been created successfully. " .
            "You can now access all training materials."
        );
    }
    
    /**
     * Login an existing user
     */
    private function loginExistingUser($user, $googleUser, $firstName, $lastName)
    {
        // Check if suspended
        if ($user->is_suspended ?? false) {
            Log::warning('Suspended user login attempt', ['user_id' => $user->id]);
            
            // Log the blocked login
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
        
        Log::info('MOH user logged in successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
        
        // Log successful login
        $emailDomain = $this->getEmailDomain($user->email);
        ActivityLogger::logAuth('google_login', "User logged in via Google OAuth", [
            'user_id' => $user->id,
            'email' => $user->email,
            'domain' => $emailDomain,
            'google_id' => $googleUser->getId(),
            'ip_address' => request()->ip(),
            'profile_updated' => !empty($updates)
        ]);
        
        return redirect()->intended('/dashboard')->with('success', 
            "Welcome back, $firstName!"
        );
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
     * Check if email domain is allowed
     */
    private function isDomainAllowed($domain)
    {
        return in_array($domain, array_map('strtolower', self::ALLOWED_DOMAINS));
    }
    
    /**
     * Try to infer department from email
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
}