<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    protected $redirectTo = '/email/verify';

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

        // Create the user locally
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name'  => $validatedData['last_name'],
            'email'      => $validatedData['email'],
            'password'   => Hash::make($validatedData['password']),
            'department' => $validatedData['department'],
            'temp_moodle_password' => encrypt($validatedData['password']),
        ]);

        // Assign default role
        $user->assignRole('user');
        
        // Store the plain password temporarily for Moodle sync
        Cache::put('moodle_temp_password_' . $user->id, $validatedData['password'], 300);
        
        // Log that password is cached for Moodle
        Log::info('New user registered, password cached for Moodle sync on first enrollment', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Send welcome email with verification link
        event(new Registered($user));
        
        // Send additional welcome email with credentials
        Mail::to($user->email)->send(new WelcomeEmail($user, $validatedData['password']));

        Auth::login($user);

        return redirect('/email/verify')
            ->with('success', 'Registration successful! Please check your email to verify your account.');
    }
}