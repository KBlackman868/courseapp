<?php

namespace App\Http\Controllers;

use App\Models\AccountRequest;
use App\Models\SystemNotification;
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
     *
     * Creates an AccountRequest with pending status.
     * User account is NOT created until admin approves.
     * No Moodle account, OTP, or welcome email until approval.
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
            // Create account request (NOT a user) - admin must approve first
            $accountRequest = AccountRequest::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'department' => 'External',
                'organization' => $validated['organization'],
                'status' => AccountRequest::STATUS_PENDING,
                'request_type' => AccountRequest::TYPE_EXTERNAL,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Log::info('External user account request created', [
                'request_id' => $accountRequest->id,
                'email' => $accountRequest->email,
            ]);

            // Log registration (wrapped to prevent cascading failures)
            try {
                ActivityLogger::log(
                    'account_request_submitted',
                    "External user account request submitted for {$accountRequest->email}",
                    $accountRequest,
                    [
                        'email' => $accountRequest->email,
                        'organization' => $accountRequest->organization,
                        'account_status' => 'pending',
                    ],
                    'success',
                    'info'
                );
            } catch (\Exception $e) {
                Log::warning('Activity logging failed during external registration', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Notify Course Admins about new request
            try {
                SystemNotification::notifyNewAccountRequest($accountRequest);
            } catch (\Exception $e) {
                Log::warning('Failed to notify admins about external registration', [
                    'error' => $e->getMessage(),
                ]);
            }

            return redirect()->route('login')->with('success',
                'Thank you for registering! Your account request has been submitted and is pending administrator approval. ' .
                'You will receive an email once your account has been approved.'
            );

        } catch (\Exception $e) {
            Log::error('External registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $validated['email'] ?? $request->input('email'),
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Registration failed. Please try again.');
        }
    }
}
