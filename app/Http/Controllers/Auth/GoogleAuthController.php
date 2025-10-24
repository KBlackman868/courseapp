<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            // Get Google user data
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('Google OAuth login attempt', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId()
            ]);
            
            // Check if user exists by email
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // User exists - update their Google ID if not set
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar() ?? $user->avatar,
                    ]);
                    Log::info('Updated existing user with Google ID', ['user_id' => $user->id]);
                }
                
                // Check if user is suspended
                if ($user->is_suspended) {
                    Log::warning('Suspended user attempted Google login', ['user_id' => $user->id]);
                    return redirect('/login')->with('error', 'Your account has been suspended. Please contact support.');
                }
                
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(24)), // Random password since they use Google
                ]);
                
                // Assign default role (student)
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('student');
                }
                
                Log::info('Created new user via Google OAuth', ['user_id' => $user->id]);
                
                // Optionally dispatch job to create Moodle account
                // if (class_exists(\App\Jobs\CreateOrLinkMoodleUser::class)) {
                //     \App\Jobs\CreateOrLinkMoodleUser::dispatch($user);
                // }
            }
            
            // Log the user in
            Auth::login($user, true); // true = remember me
            
            // Regenerate session for security
            request()->session()->regenerate();
            
            Log::info('User logged in successfully via Google', ['user_id' => $user->id]);
            
            // Redirect to intended page or dashboard
            return redirect()->intended('/dashboard')->with('success', 'Welcome ' . $user->name . '!');
            
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error('Google OAuth Invalid State', ['error' => $e->getMessage()]);
            return redirect('/login')->with('error', 'Authentication failed. Please try again.');
            
        } catch (\Exception $e) {
            Log::error('Google OAuth Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', 'Unable to login with Google. Please try again or use email/password.');
        }
    }
}