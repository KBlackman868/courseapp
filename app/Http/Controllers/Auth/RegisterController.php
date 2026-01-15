<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Mail\WelcomeEmail;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    protected $redirectTo = '/auth/otp/verify';
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function showRegistrationForm()
    {
        return view('pages.home_register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'department' => 'required|string|max:255',
        ]);

        // Create the user with verification tracking
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name'  => $validatedData['last_name'],
            'email'      => $validatedData['email'],
            'password'   => Hash::make($validatedData['password']),
            'department' => $validatedData['department'],
            'temp_moodle_password' => encrypt($validatedData['password']),
            'verification_status' => 'pending',
            'verification_sent_at' => now(),
            'verification_attempts' => 1,
            'must_verify_before' => now()->addHours(48),
            'initial_otp_completed' => false,
        ]);

        // Assign default role
        $user->assignRole('user');

        // Store the plain password temporarily for Moodle sync
        Cache::put('moodle_temp_password_' . $user->id, $validatedData['password'], 300);

        Log::info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'verification_status' => 'pending',
            'must_verify_before' => $user->must_verify_before,
        ]);

        // Send OTP for verification instead of email link
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
        Mail::to($user->email)->queue(new WelcomeEmail($user, $validatedData['password']));

        // Clear any session messages to prevent duplicates
        session()->forget(['success', 'error', 'message']);

        // Redirect to OTP verification page
        return redirect()->route('auth.otp.verify')
            ->with('success', 'Registration successful! Please enter the verification code sent to your email.');
    }
}