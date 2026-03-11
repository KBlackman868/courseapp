<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AccountRequest;
use App\Models\SystemNotification;
use App\Mail\VerifyEmailMail;
use App\Rules\PasswordRules;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * RegisterController
 *
 * REGISTRATION WORKFLOW (both user types):
 * 1. User submits form → AccountRequest created (status: pending_verification)
 * 2. Verification email sent with 24-hour signed link
 * 3. User clicks link → email verified
 * 4. MOH Staff: auto-approved → User + Moodle created → welcome email → can log in
 * 5. External: status = email_verified → admin must approve
 */
class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
       
        try {
            // Clean up orphaned records from previous failed attempts.
            // This handles the case where auto-approval partially succeeded (User created
            // but account_request stuck at email_verified due to missing transaction).
            $email = $request->input('email');
            if ($email) {
                $this->cleanUpOrphanedRecords($email);
            }

            try{
            $validatedData = $request->validate([
                'first_name'  => 'required|string|max:255',
                'last_name'   => 'required|string|max:255',
                'email'       => [
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
                'department'  => 'required|string|max:255',
                'password'    => PasswordRules::rules($request),
                'terms'       => 'accepted',
            ], array_merge(PasswordRules::messages(), [
                'email.unique' => 'This email is already registered or has a pending request.',
                'terms.accepted' => 'You must agree to the Terms and Conditions.',
            ]));
            }
            catch(\Illuminate\Validation\ValidationException $e) {
                // Log validation errors for debugging
                Log::warning('Registration validation failed', [
                    'errors' => $e->errors(),
                    'input' => $request->except('password', 'password_confirmation'),
                ]);
                throw $e; // Let Laravel handle the response (422 with error messages)
            }
            $isMoh = User::isMohEmail($validatedData['email']);

            // Create the account request
            $accountRequest = AccountRequest::create([
                'first_name'   => $validatedData['first_name'],
                'last_name'    => $validatedData['last_name'],
                'email'        => $validatedData['email'],
                'password'     => Hash::make($validatedData['password']),
                'department'   => $validatedData['department'],
                'organization' => $validatedData['department'],
                'status'       => AccountRequest::STATUS_PENDING_VERIFICATION,
                'request_type' => $isMoh ? AccountRequest::TYPE_MOH_STAFF : AccountRequest::TYPE_EXTERNAL,
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
            ]);

            Log::info('Account request created', [
                'request_id' => $accountRequest->id,
                'email'      => $accountRequest->email,
                'type'       => $accountRequest->request_type,
            ]);

            // Send verification email
            $this->sendVerificationEmail($accountRequest);

            // Log the action
            try {
                ActivityLogger::log(
                    'account_request_submitted',
                    "Registration request submitted for {$accountRequest->email}",
                    $accountRequest,
                    [
                        'email'        => $accountRequest->email,
                        'request_type' => $accountRequest->request_type,
                        'department'   => $accountRequest->department,
                    ],
                    'success',
                    'info'
                );
            } catch (\Exception $e) {
                Log::warning('Activity logging failed', ['error' => $e->getMessage()]);
            }

            return Inertia::render('Auth/RegistrationPending', [
                'email' => $accountRequest->email,
                'type'  => $accountRequest->request_type,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Let Laravel handle validation errors normally (422)

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->input('email'),
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    /**
     * 1. Old rejected/pending account requests blocking the DB unique constraint
     * 2. Orphaned User + stuck account_request from a failed auto-approval
     *    (User was created but account_request never updated to 'approved')
     */
    private function cleanUpOrphanedRecords(string $email): void
    {
        // Delete old rejected or legacy pending account requests
        AccountRequest::where('email', $email)
            ->whereIn('status', [
                AccountRequest::STATUS_REJECTED,
                AccountRequest::STATUS_PENDING,
            ])
            ->delete();

        // Check for orphaned state: account_request at email_verified but
        // a User already exists (from a failed auto-approval transaction)
        $stuckRequest = AccountRequest::where('email', $email)
            ->where('status', AccountRequest::STATUS_EMAIL_VERIFIED)
            ->first();

        if ($stuckRequest) {
            Log::warning('Cleaning up stuck account request from failed auto-approval', [
                'email' => $email,
                'account_request_id' => $stuckRequest->id,
            ]);

            // Check if a User was also orphaned (created before the transaction failed)
            $orphanedUser = User::where('email', $email)->first();
            if ($orphanedUser) {
                // Remove role assignments first to avoid FK constraint violations
                $orphanedUser->roles()->detach();
                $orphanedUser->permissions()->detach();
                $orphanedUser->delete();
            }

            // Always delete the stuck account request so re-registration can proceed
            $stuckRequest->delete();
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

    /**
     * Resend verification email (from the expired page).
     */
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $accountRequest = AccountRequest::where('email', $request->email)
            ->where('status', AccountRequest::STATUS_PENDING_VERIFICATION)
            ->latest()
            ->first();

        if (!$accountRequest) {
            return back()->withErrors(['email' => 'No pending registration found for this email.']);
        }

        $this->sendVerificationEmail($accountRequest);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Verification email resent.']);
        }

        return Inertia::render('Auth/RegistrationPending', [
            'email'  => $accountRequest->email,
            'type'   => $accountRequest->request_type,
            'resent' => true,
        ]);
    }

    // =========================================================================
    // MOH STAFF DEDICATED FORM (blade-based, unchanged)
    // =========================================================================

    public function showMohRequestForm()
    {
        return view('auth.moh-request-account');
    }

    public function submitMohRequest(Request $request)
    {
        $validatedData = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => [
                'required', 'string', 'email', 'max:255',
                'unique:users',
                Rule::unique('account_requests', 'email')->where(
                    fn ($query) => $query->whereIn('status', [
                        AccountRequest::STATUS_PENDING_VERIFICATION,
                        AccountRequest::STATUS_EMAIL_VERIFIED,
                        AccountRequest::STATUS_APPROVED,
                    ])
                ),
                function ($attribute, $value, $fail) {
                    if (!User::isMohEmail($value)) {
                        $fail('Only @' . User::MOH_EMAIL_DOMAIN . ' email addresses can request MOH Staff accounts.');
                    }
                },
            ],
            'password'       => PasswordRules::rules($request),
            'department'     => 'required|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'date_of_birth'  => 'required|date|before_or_equal:' . now()->subYears(18)->toDateString(),
        ], array_merge(PasswordRules::messages(), [
            'date_of_birth.before_or_equal' => 'You must be at least 18 years old to register.',
        ]));

        $accountRequest = AccountRequest::create([
            'first_name'   => $validatedData['first_name'],
            'last_name'    => $validatedData['last_name'],
            'email'        => $validatedData['email'],
            'password'     => Hash::make($validatedData['password']),
            'department'   => $validatedData['department'],
            'organization' => 'Ministry of Health Trinidad and Tobago',
            'phone'        => $validatedData['phone'] ?? null,
            'status'       => AccountRequest::STATUS_PENDING_VERIFICATION,
            'request_type' => AccountRequest::TYPE_MOH_STAFF,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);

        $this->sendVerificationEmail($accountRequest);

        Log::info('MOH Staff account request submitted via dedicated form', [
            'request_id' => $accountRequest->id,
            'email'      => $accountRequest->email,
        ]);

        try {
            ActivityLogger::log(
                'account_request_submitted',
                "MOH Staff account request submitted for {$accountRequest->email}",
                $accountRequest,
                ['email' => $accountRequest->email, 'department' => $accountRequest->department],
                'success',
                'info'
            );
        } catch (\Exception $e) {
            Log::warning('Activity logging failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('moh.request-submitted');
    }

    public function mohRequestSubmitted()
    {
        return view('auth.moh-request-submitted');
    }
}
