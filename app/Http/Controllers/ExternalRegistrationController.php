<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
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
        Log::info('External registration attempt', ['email' => $request->input('email')]);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                'unique:users',
                Rule::unique('account_requests', 'email')->where(fn ($query) => $query->where('status', 'pending')),
            ],
            'organization' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()],
            'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
        ], [
            'password.min' => 'Password must be at least 12 characters for standard accounts.',
            'date_of_birth.before_or_equal' => 'You must be at least 18 years old to register.',
        ]);

        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'email' => $validated['email'],
                'organization' => $validated['organization'],
                'department' => 'External',
                'password' => $validated['password'],
                'user_type' => User::TYPE_EXTERNAL,
                'account_status' => User::STATUS_PENDING,
                'auth_method' => 'local',
            ]);

            // Assign external_user role if it exists
            try {
                if (class_exists(\Spatie\Permission\Models\Role::class)) {
                    $role = \Spatie\Permission\Models\Role::where('name', 'external_user')->first();
                    if ($role) {
                        $user->assignRole($role);
                    } else {
                        $userRole = \Spatie\Permission\Models\Role::where('name', 'user')->first();
                        if ($userRole) {
                            $user->assignRole($userRole);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Role assignment failed during external registration', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Log registration (wrapped to prevent cascading failures)
            try {
                ActivityLogger::logAuth('external_registration',
                    "External user registered: {$user->email}",
                    [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'organization' => $user->organization,
                        'account_status' => 'pending',
                    ]
                );
            } catch (\Exception $e) {
                Log::warning('Activity logging failed during external registration', [
                    'error' => $e->getMessage(),
                ]);
            }

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
                'trace' => $e->getTraceAsString(),
                'email' => $validated['email'] ?? $request->input('email'),
            ]);

            try {
                ActivityLogger::logAuth('external_registration_failed',
                    "External registration failed for: {$validated['email']}",
                    ['error' => $e->getMessage()]
                );
            } catch (\Exception $logException) {
                Log::warning('Activity logging also failed', ['error' => $logException->getMessage()]);
            }

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Registration failed. Please try again.');
        }
    }
}
