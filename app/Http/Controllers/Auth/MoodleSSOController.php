<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Services\ActivityLogger;
use App\Services\MoodleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

/**
 * Handles SSO login flow for the Moodle mobile app.
 *
 * When Moodle is configured with $CFG->alternateloginurl pointing to mohlearn,
 * both desktop browsers and the Moodle mobile app are redirected here to
 * authenticate. After the user logs in on mohlearn, this controller generates
 * an auth_userkey SSO URL and sends them back to Moodle, fully authenticated.
 *
 * Flow:
 *   1. Moodle redirects to /moodle/sso/login (with ?wantsurl=... from Moodle)
 *   2. If not logged in → show mohlearn login page (with redirect back after login)
 *   3. If logged in → ensure Moodle account exists → generate userkey URL → redirect to Moodle
 */
class MoodleSSOController extends Controller
{
    /**
     * Entry point for Moodle SSO redirects.
     *
     * Moodle's $CFG->alternateloginurl sends users here. The mobile app also
     * lands here when "login via browser window" is configured.
     *
     * Query params Moodle may send:
     *   - wantsurl: the Moodle page the user wanted to visit
     *   - service:  "moodle_mobile_app" when coming from the mobile app
     */
    public function login(Request $request)
    {
        // Capture where the user wants to go in Moodle after authentication
        $wantsUrl = $request->query('wantsurl');
        $service = $request->query('service');

        // Store these in session so they survive the login redirect
        if ($wantsUrl) {
            session(['moodle_sso_wantsurl' => $wantsUrl]);
        }
        if ($service) {
            session(['moodle_sso_service' => $service]);
        }

        // If user is not authenticated on mohlearn, send them to login first.
        // After login, Laravel's "intended" redirect will bring them back here.
        if (!Auth::check()) {
            // Store this URL as the intended destination so after login
            // the user comes back here with the same query params
            $currentUrl = $request->fullUrl();
            session(['url.intended' => $currentUrl]);

            return redirect()->route('login');
        }

        // User is authenticated — proceed with SSO
        return $this->handleSSORedirect($request);
    }

    /**
     * Generate the Moodle SSO URL and redirect the user.
     *
     * This is called after the user is authenticated on mohlearn.
     * It ensures the user has a Moodle account, then generates a one-time
     * auth_userkey login URL and redirects them to Moodle.
     */
    private function handleSSORedirect(Request $request)
    {
        $user = Auth::user();

        // Step 1: Ensure user has a Moodle account with auth=userkey
        try {
            CreateOrLinkMoodleUser::dispatchSync($user);
            $user->refresh();
        } catch (\Exception $e) {
            Log::error('Moodle SSO: Failed to create/link Moodle account', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Could not set up your Moodle account. Please try again or contact support.');
        }

        if (!$user->moodle_user_id) {
            Log::error('Moodle SSO: User has no Moodle ID after sync', [
                'user_id' => $user->id,
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Could not create your Moodle account. Please contact a system administrator.');
        }

        // Step 2: Build the redirect URL for after Moodle login
        $wantsUrl = session('moodle_sso_wantsurl');
        $service = session('moodle_sso_service');

        // Clean up session
        session()->forget(['moodle_sso_wantsurl', 'moodle_sso_service']);

        // Default to Moodle dashboard if no specific page requested
        $moodleBaseUrl = config('moodle.base_url');
        $redirectUrl = $wantsUrl ?: $moodleBaseUrl . '/my/';

        // Step 3: Generate the auth_userkey SSO login URL
        try {
            $moodleService = app(MoodleService::class);

            $username = $user->username ?? explode('@', $user->email)[0];
            $userData = [
                'username' => $username,
                'email' => $user->email,
                'firstname' => $user->first_name ?? 'User',
                'lastname' => $user->last_name ?? 'User',
            ];

            $loginUrl = $moodleService->generateLoginUrl($userData, $redirectUrl);

            if (!$loginUrl) {
                throw new \Exception('SSO login URL could not be generated.');
            }

            // If this is from the mobile app, we need to ensure the login URL
            // will work with the Moodle mobile app's token exchange.
            // The mobile app intercepts the redirect after successful browser auth
            // and extracts the session to generate a mobile token.
            if ($service === 'moodle_mobile_app') {
                Log::info('Moodle SSO: Mobile app login via browser', [
                    'user_id' => $user->id,
                    'moodle_user_id' => $user->moodle_user_id,
                ]);
            }

            ActivityLogger::logMoodle('sso_login',
                "User authenticated via Moodle SSO redirect",
                null,
                [
                    'user_id' => $user->id,
                    'moodle_user_id' => $user->moodle_user_id,
                    'source' => $service === 'moodle_mobile_app' ? 'mobile_app' : 'browser',
                ]
            );

            return redirect()->away($loginUrl);

        } catch (\Exception $e) {
            Log::error('Moodle SSO: Failed to generate login URL', [
                'user_id' => $user->id,
                'moodle_user_id' => $user->moodle_user_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Could not log you into Moodle automatically. Please try accessing your course from the dashboard.');
        }
    }
}
