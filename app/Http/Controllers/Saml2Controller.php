<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use SimpleSAML\Auth\Simple;
use SimpleSAML\Configuration;

class Saml2Controller extends Controller
{
    private $samlAuth;

    public function __construct()
    {
        // Initialize SimpleSAMLphp
        $this->samlAuth = new Simple('default-sp');
    }

    /**
     * Handle direct course access with SSO
     */
    public function directCourseAccess(Course $course)
    {
        // Check if user is already authenticated
        if (!Auth::check()) {
            // Store intended course in session
            Session::put('intended_course_id', $course->id);
            Session::put('intended_moodle_course_id', $course->moodle_course_id);
            
            // Redirect to SAML login
            return redirect()->route('saml2.login');
        }

        // User is authenticated, check enrollment
        $user = Auth::user();
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'You are not enrolled in this course.');
        }

        // Build Moodle URL with SSO token
        return $this->redirectToMoodleCourse($user, $course);
    }

    /**
     * SAML2 Login
     */
    public function login(Request $request)
    {
        // Store the return URL or intended course
        $returnTo = $request->get('returnTo', route('dashboard'));
        
        if (Session::has('intended_course_id')) {
            $courseId = Session::get('intended_course_id');
            $returnTo = route('saml2.course.callback', ['course' => $courseId]);
        }

        // Require SAML authentication
        $this->samlAuth->requireAuth([
            'ReturnTo' => $returnTo,
            'KeepPost' => false,
        ]);

        // Get SAML attributes
        $attributes = $this->samlAuth->getAttributes();

        // Find or create user based on SAML attributes
        $user = $this->findOrCreateUser($attributes);

        // Login the user
        Auth::login($user, true);
        request()->session()->regenerate(); // SECURITY: Prevent session fixation attacks

        // Redirect to intended destination
        return redirect($returnTo);
    }

    /**
     * SAML2 Course Callback - handles return from SAML with course intent
     */
    public function courseCallback(Course $course)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Clear session variables
        Session::forget('intended_course_id');
        Session::forget('intended_moodle_course_id');

        return $this->redirectToMoodleCourse(Auth::user(), $course);
    }

    /**
     * Build and redirect to Moodle with SSO token
     */
    private function redirectToMoodleCourse(User $user, Course $course)
    {
        // Generate SSO token for Moodle
        $ssoToken = $this->generateSSOToken($user);
        
        // Build Moodle URL
        $moodleBaseUrl = config('moodle.url', 'https://learnabouthealth.hin.gov.tt');
        $courseUrl = $moodleBaseUrl . '/course/view.php';
        
        $params = [
            'id' => $course->moodle_course_id,
            'ssotoken' => $ssoToken,
            'userid' => $user->moodle_user_id,
        ];

        $moodleUrl = $courseUrl . '?' . http_build_query($params);

        Log::info('Redirecting to Moodle course', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'moodle_course_id' => $course->moodle_course_id,
            'url' => $moodleUrl
        ]);

        return redirect()->away($moodleUrl);
    }

    /**
     * Generate SSO token for Moodle authentication
     */
    private function generateSSOToken(User $user)
    {
        // Generate a secure token
        $token = bin2hex(random_bytes(32));
        
        // Store token in cache with 5-minute expiry
        $cacheKey = 'moodle_sso_' . $token;
        cache()->put($cacheKey, [
            'user_id' => $user->id,
            'moodle_user_id' => $user->moodle_user_id,
            'email' => $user->email,
            'timestamp' => now()->timestamp,
        ], 300); // 5 minutes

        return $token;
    }

    /**
     * Find or create user from SAML attributes
     */
    private function findOrCreateUser($attributes)
    {
        $email = $attributes['email'][0] ?? $attributes['mail'][0] ?? null;
        
        if (!$email) {
            throw new \Exception('No email found in SAML attributes');
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $user = User::create([
                'email' => $email,
                'first_name' => $attributes['givenName'][0] ?? '',
                'last_name' => $attributes['sn'][0] ?? '',
                'department' => $attributes['department'][0] ?? 'Unknown',

                'password' => bcrypt(str()->random(32)), // Random password for SSO users
            ]);
            
            $user->assignRole('user');
        }

        return $user;
    }

    /**
     * SAML2 Logout
     */
    public function logout()
    {
        Auth::logout();
        
        $this->samlAuth->logout([
            'ReturnTo' => route('home'),
        ]);
    }

    /**
     * SAML2 Metadata
     */
    public function metadata()
    {
        $config = Configuration::getInstance();
        $metadata = $config->getMetadataURL();
        
        return response($metadata)
            ->header('Content-Type', 'application/xml');
    }
}