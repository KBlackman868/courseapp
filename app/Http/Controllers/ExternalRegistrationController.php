<?php

namespace App\Http\Controllers;

use App\Models\AccountRequest;
use App\Models\SystemNotification;
use App\Models\User;
use App\Mail\VerifyEmailMail;
use App\Rules\PasswordRules;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

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
     * Creates an AccountRequest with pending_verification status.
     * Sends verification email with 24-hour signed link.
     * User account is NOT created until admin approves (after email verification).
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
                Rule::unique('account_requests', 'email')->where(
                    fn ($query) => $query->whereIn('status', [
                        AccountRequest::STATUS_PENDING_VERIFICATION,
                        AccountRequest::STATUS_EMAIL_VERIFIED,
                        AccountRequest::STATUS_APPROVED,
                    ])
                ),
            ],
            'organization' => ['required', 'string', 'max:255'],
            'password' => PasswordRules::rules($request),
            'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
        ], array_merge(PasswordRules::messages(), [
            'email.unique' => 'This email is already registered or has a pending request.',
            'date_of_birth.before_or_equal' => 'You must be at least 18 years old to register.',
        ]));

        try {
            $accountRequest = AccountRequest::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'department' => 'External',
                'organization' => $validated['organization'],
                'status' => AccountRequest::STATUS_PENDING_VERIFICATION,
                'request_type' => AccountRequest::TYPE_EXTERNAL,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Log::info('External user account request created', [
                'request_id' => $accountRequest->id,
                'email' => $accountRequest->email,
            ]);

            // Send verification email
            $this->sendVerificationEmail($accountRequest);

            // Log registration
            try {
                ActivityLogger::log(
                    'account_request_submitted',
                    "External user account request submitted for {$accountRequest->email}",
                    $accountRequest,
                    [
                        'email' => $accountRequest->email,
                        'organization' => $accountRequest->organization,
                    ],
                    'success',
                    'info'
                );
            } catch (\Exception $e) {
                Log::warning('Activity logging failed during external registration', [
                    'error' => $e->getMessage(),
                ]);
            }

            return redirect()->route('login')->with('success',
                'Thank you for registering! A verification email has been sent to ' . $accountRequest->email . '. ' .
                'Please click the link in the email within 24 hours to verify your address.'
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

    /**
     * Send the verification email with a 24-hour signed link.
     */
    private function sendVerificationEmail(AccountRequest $accountRequest): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify-email',
            now()->addHours(24),
            [
                'id'   => $accountRequest->id,
                'hash' => sha1($accountRequest->email),
            ]
        );

        try {
            Mail::to($accountRequest->email)->send(
                new VerifyEmailMail($accountRequest, $verificationUrl)
            );

            Log::info('Verification email sent', [
                'request_id' => $accountRequest->id,
                'email'      => $accountRequest->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send verification email', [
                'request_id' => $accountRequest->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
