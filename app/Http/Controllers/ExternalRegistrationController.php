<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class ExternalRegistrationController extends Controller
{
    /**
     * Show the external user registration form
     */
    public function create()
    {
        return view('auth.register-external');
    }

    /**
     * Handle external user registration
     * Creates user with pending account_status
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'organization' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'organization' => $validated['organization'],
                'password' => Hash::make($validated['password']),
                'user_type' => User::TYPE_EXTERNAL,
                'account_status' => User::STATUS_PENDING,
                'auth_method' => 'local',
            ]);

            // Assign external_user role if it exists
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $role = \Spatie\Permission\Models\Role::where('name', 'external_user')->first();
                if ($role) {
                    $user->assignRole($role);
                } else {
                    // Fallback to user role
                    $userRole = \Spatie\Permission\Models\Role::where('name', 'user')->first();
                    if ($userRole) {
                        $user->assignRole($userRole);
                    }
                }
            }

            // Log registration
            ActivityLogger::logAuth('external_registration',
                "External user registered: {$user->email}",
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'organization' => $user->organization,
                    'account_status' => 'pending',
                ]
            );

            Log::info('External user registered', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('login')->with('success',
                'Your registration has been submitted. Please wait for an administrator to approve your account before you can log in.'
            );

        } catch (\Exception $e) {
            Log::error('External registration failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'],
            ]);

            ActivityLogger::logAuth('external_registration_failed',
                "External registration failed for: {$validated['email']}",
                ['error' => $e->getMessage()],
                'failed',
                'error'
            );

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Registration failed. Please try again.');
        }
    }
}
