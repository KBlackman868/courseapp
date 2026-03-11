<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Mail\WelcomeEmail;
use App\Models\AccountRequest;
use App\Models\SystemNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

/**
 * Handles signed-URL email verification for account requests.
 *
 * MOH Staff (@health.gov.tt): auto-approved on verification
 * External Users: moves to 'email_verified' → admin must approve
 */
class EmailVerificationController extends Controller
{
    /**
     * Handle the email verification link click.
     *
     * Route: GET /email/verify-registration/{id}/{hash}
     * Middleware: signed (validated in route definition)
     */
    public function verify(Request $request, $id, $hash)
    {
        $accountRequest = AccountRequest::find($id);

        if (!$accountRequest) {
            return Inertia::render('Auth/VerificationExpired', [
                'reason' => 'not_found',
            ]);
        }

        // Check the hash matches
        if ($hash !== sha1($accountRequest->email)) {
            return Inertia::render('Auth/VerificationExpired', [
                'reason' => 'invalid',
            ]);
        }

        // Already verified or approved?
        if ($accountRequest->isApproved()) {
            return redirect()->route('login')->with('success', 'Your account is already active. Please log in.');
        }

        if ($accountRequest->isEmailVerified() && $accountRequest->status === AccountRequest::STATUS_EMAIL_VERIFIED) {
            return Inertia::render('Auth/RegistrationPending', [
                'email'    => $accountRequest->email,
                'type'     => $accountRequest->request_type,
                'verified' => true,
            ]);
        }

        // Not in pending_verification state? Might be rejected or other
        if (!$accountRequest->isPendingVerification()) {
            return Inertia::render('Auth/VerificationExpired', [
                'reason' => 'already_processed',
            ]);
        }

        // Mark email as verified
        $accountRequest->markEmailVerified();

        Log::info('Email verified for account request', [
            'request_id' => $accountRequest->id,
            'email'      => $accountRequest->email,
            'type'       => $accountRequest->request_type,
        ]);

        // MOH Staff → auto-approve
        if ($accountRequest->isMohStaffRequest()) {
            return $this->autoApproveMohStaff($accountRequest);
        }

        // External → show "pending admin approval" page
        try {
            // Notify admins that an external user verified their email
            SystemNotification::create([
                'user_id' => null, // System notification — will be shown to admins
                'title'   => 'New External User Awaiting Approval',
                'message' => "External user {$accountRequest->full_name} ({$accountRequest->email}) has verified their email and is awaiting approval.",
                'type'    => 'account_request',
                'data'    => json_encode(['request_id' => $accountRequest->id]),
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to create admin notification', ['error' => $e->getMessage()]);
        }

        try {
            ActivityLogger::log(
                'email_verified',
                "External user email verified: {$accountRequest->email}",
                $accountRequest,
                ['email' => $accountRequest->email],
                'success',
                'info'
            );
        } catch (\Exception $e) {
            Log::warning('Activity logging failed', ['error' => $e->getMessage()]);
        }

        return Inertia::render('Auth/RegistrationPending', [
            'email'    => $accountRequest->email,
            'type'     => 'external',
            'verified' => true,
        ]);
    }

    /**
     * Auto-approve MOH staff after email verification.
     */
    private function autoApproveMohStaff(AccountRequest $accountRequest)
    {
        try {
            // Find any admin-level user for the audit trail
            $systemReviewer = \App\Models\User::whereHas('roles', fn ($q) => $q->whereIn('name', ['superadmin', 'admin']))
                ->first();

            if ($systemReviewer) {
                $user = $accountRequest->approve(
                    $systemReviewer,
                    'Auto-approved: MOH staff email verified'
                );
            } else {
                // No admin exists yet — approve without a reviewer (wrapped in transaction)
                $user = \Illuminate\Support\Facades\DB::transaction(function () use ($accountRequest) {
                    $role = \App\Models\User::ROLE_MOH_STAFF;

                    $user = \App\Models\User::create([
                        'first_name' => $accountRequest->first_name,
                        'last_name' => $accountRequest->last_name,
                        'email' => $accountRequest->email,
                        'password' => $accountRequest->password,
                        'department' => $accountRequest->department,
                        'organization' => $accountRequest->organization,
                        'user_type' => \App\Models\User::TYPE_INTERNAL,
                        'account_status' => \App\Models\User::STATUS_ACTIVE,
                        'email_verified_at' => $accountRequest->email_verified_at ?? now(),
                        'verification_status' => 'verified',
                        'auth_method' => 'local',
                    ]);

                    $user->assignRole($role);

                    $accountRequest->update([
                        'status' => AccountRequest::STATUS_APPROVED,
                        'reviewed_by' => null,
                        'reviewed_at' => now(),
                        'admin_notes' => 'Auto-approved: MOH staff email verified (no admin user available)',
                        'user_id' => $user->id,
                    ]);

                    return $user;
                });
            }

            // Queue Moodle account creation
            try {
                CreateOrLinkMoodleUser::dispatch($user);
            } catch (\Exception $e) {
                Log::warning('Failed to dispatch Moodle job', ['error' => $e->getMessage()]);
            }

            // Send welcome email
            try {
                Mail::to($user->email)->queue(
                    new WelcomeEmail($user, null) // password already set
                );
            } catch (\Exception $e) {
                Log::warning('Failed to send welcome email', ['error' => $e->getMessage()]);
            }

            // Notify admins (informational)
            try {
                SystemNotification::create([
                    'user_id' => null,
                    'title'   => 'New MOH Staff Registered',
                    'message' => "New MOH Staff member registered: {$user->first_name} {$user->last_name} ({$user->email}) — {$user->department}",
                    'type'    => 'info',
                    'data'    => json_encode(['user_id' => $user->id]),
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create admin notification', ['error' => $e->getMessage()]);
            }

            ActivityLogger::log(
                'moh_staff_auto_approved',
                "MOH Staff auto-approved after email verification: {$user->email}",
                $user,
                ['user_id' => $user->id, 'email' => $user->email, 'department' => $user->department],
                'success',
                'info'
            );

            Log::info('MOH Staff auto-approved', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return redirect()->route('login')->with('success',
                'Your email has been verified and your MOH staff account is now active. You can log in with the password you created during registration.'
            );

        } catch (\Exception $e) {
            Log::error('MOH staff auto-approval failed', [
                'request_id' => $accountRequest->id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return Inertia::render('Auth/RegistrationPending', [
                'email'    => $accountRequest->email,
                'type'     => 'moh_staff',
                'verified' => true,
                'error'    => 'Email verified but account activation failed. An administrator has been notified.',
            ]);
        }
    }

    /**
     * Show the expired verification link page.
     */
    public function expired()
    {
        return Inertia::render('Auth/VerificationExpired');
    }
}
