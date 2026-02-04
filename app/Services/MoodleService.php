<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        // Read from config/moodle.php
        $this->baseUrl = config('moodle.base_url');
        $this->token = config('moodle.token');

        // Remove trailing slash from base URL if present
        $this->baseUrl = rtrim($this->baseUrl, '/');
    }

    /**
     * Make a call to Moodle Web Service API
     *
     * @param string $function The Moodle web service function to call
     * @param array $params Parameters to send to the function
     * @return mixed Response from Moodle
     * @throws \Exception
     */
    public function call($function, array $params = [])
    {
        $url = $this->baseUrl . '/webservice/rest/server.php';
        
        // Prepare parameters
        $requestParams = array_merge($params, [
            'wstoken' => $this->token,
            'wsfunction' => $function,
            'moodlewsrestformat' => 'json'
        ]);

        try {
            // Create HTTP client with SSL verification setting
            $client = Http::asForm();
            
            // Check if SSL verification should be disabled
            if (env('MOODLE_VERIFY_SSL', true) === false || env('MOODLE_VERIFY_SSL', true) === 'false') {
                $client = $client->withoutVerifying();
            }
            
            $response = $client->post($url, $requestParams);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Check for Moodle errors
                if (isset($data['exception'])) {
                    throw new \Exception("Moodle error: " . ($data['message'] ?? 'Unknown error'));
                }
                
                return $data;
            } else {
                throw new \Exception("HTTP request failed with status: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Moodle API call failed', [
                'function' => $function,
                'error' => $e->getMessage(),
                'params' => $params
            ]);
            throw $e;
        }
    }

    /**
     * Create a user in Moodle
     *
     * @param array $userData User data
     * @return int|null Moodle user ID or null on failure
     */
    public function createUser(array $userData)
    {
        try {
            // Ensure required fields are present
            $requiredFields = ['username', 'password', 'firstname', 'lastname', 'email'];
            foreach ($requiredFields as $field) {
                if (!isset($userData[$field]) || empty($userData[$field])) {
                    throw new \Exception("Missing required field: {$field}");
                }
            }

            // Call Moodle's core_user_create_users function
            $response = $this->call('core_user_create_users', [
                'users' => [$userData]
            ]);

            // Check if user was created successfully
            if (isset($response[0]['id'])) {
                // Success - user created
                return $response[0]['id'];
            }

            // Check for errors in response
            if (isset($response['exception'])) {
                throw new \Exception("Moodle API Error: " . ($response['message'] ?? 'Unknown error'));
            }

            // Check for username already exists error
            if (isset($response[0]) && !isset($response[0]['id'])) {
                // This might be an error response
                throw new \Exception("Moodle user creation failed. Response: " . json_encode($response));
            }

            throw new \Exception("Unexpected Moodle response: " . json_encode($response));

        } catch (\Exception $e) {
            // Re-throw the exception so RegisterController can catch and display it
            throw $e;
        }
    }

    /**
     * Get a user by username from Moodle
     *
     * @param string $username
     * @return array|null User data or null if not found
     */
    public function getUserByUsername($username)
    {
        try {
            $response = $this->call('core_user_get_users', [
                'criteria' => [
                    [
                        'key' => 'username',
                        'value' => $username
                    ]
                ]
            ]);

            if (isset($response['users']) && count($response['users']) > 0) {
                return $response['users'][0];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get Moodle user', [
                'error' => $e->getMessage(),
                'username' => $username
            ]);
            return null;
        }
    }

    /**
     * Enroll a user in a Moodle course
     *
     * @param int $userId Moodle user ID
     * @param int $courseId Moodle course ID
     * @param int $roleId Role ID (5 = student, 3 = teacher)
     * @return bool Success status
     */
    public function enrollUserInCourse($userId, $courseId, $roleId = 5)
    {
        try {
            $response = $this->call('enrol_manual_enrol_users', [
                'enrolments' => [
                    [
                        'roleid' => $roleId,
                        'userid' => $userId,
                        'courseid' => $courseId
                    ]
                ]
            ]);

            // If no exception was thrown, enrollment was successful
            Log::info('User enrolled in Moodle course', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'role_id' => $roleId
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to enroll user in Moodle course', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'course_id' => $courseId
            ]);
            return false;
        }
    }

    /**
     * Unenroll a user from a Moodle course
     *
     * @param int $userId Moodle user ID
     * @param int $courseId Moodle course ID
     * @return bool Success status
     */
    public function unenrollUserFromCourse($userId, $courseId)
    {
        try {
            $response = $this->call('enrol_manual_unenrol_users', [
                'enrolments' => [
                    [
                        'userid' => $userId,
                        'courseid' => $courseId
                    ]
                ]
            ]);
            
            Log::info('User unenrolled from Moodle course', [
                'user_id' => $userId,
                'course_id' => $courseId
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to unenroll user from Moodle course', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'course_id' => $courseId
            ]);
            return false;
        }
    }

    /**
     * Create a course in Moodle
     *
     * @param array $courseData Course data
     * @return int|null Moodle course ID or null on failure
     */
    public function createCourse(array $courseData)
    {
        try {
            // Ensure required fields
            $requiredFields = ['fullname', 'shortname', 'categoryid'];
            foreach ($requiredFields as $field) {
                if (!isset($courseData[$field]) || empty($courseData[$field])) {
                    throw new \Exception("Missing required field: {$field}");
                }
            }

            // Set default format if not provided
            if (!isset($courseData['format'])) {
                $courseData['format'] = 'topics';
            }

            $response = $this->call('core_course_create_courses', [
                'courses' => [$courseData]
            ]);

            if (isset($response[0]['id'])) {
                Log::info('Moodle course created successfully', [
                    'moodle_course_id' => $response[0]['id'],
                    'shortname' => $courseData['shortname']
                ]);
                return $response[0]['id'];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to create Moodle course', [
                'error' => $e->getMessage(),
                'shortname' => $courseData['shortname'] ?? 'unknown'
            ]);
            return null;
        }
    }

    /**
     * Get all course categories from Moodle
     *
     * @return array List of categories
     */
    public function getCategories()
    {
        try {
            $response = $this->call('core_course_get_categories');
            return $response ?: [];
        } catch (\Exception $e) {
            Log::error('Failed to get Moodle categories', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Test the Moodle connection
     *
     * @return bool Connection status
     */
    public function testConnection()
    {
        try {
            // Try to get site info as a connection test
            $response = $this->call('core_webservice_get_site_info');
            return isset($response['sitename']);
        } catch (\Exception $e) {
            Log::error('Moodle connection test failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate an auto-login URL for a user using auth_userkey plugin
     * This allows users to be automatically logged into Moodle
     *
     * @param array $userData User data with username, email, firstname, lastname
     * @param string|null $redirectUrl Optional URL to redirect after login (e.g., course page)
     * @return string|null The auto-login URL or null on failure
     */
    public function generateLoginUrl(array $userData, ?string $redirectUrl = null): ?string
    {
        try {
            // Send all user fields - auth_userkey may match on any of these
            $params = ['user' => $userData];
            $response = null;
            $functionName = null;

            // Try the newer function first
            try {
                $response = $this->call('auth_userkey_request_login_url', $params);
                $functionName = 'auth_userkey_request_login_url';
            } catch (\Exception $e) {
                Log::info('auth_userkey_request_login_url not available, trying generatekey', [
                    'error' => $e->getMessage()
                ]);
            }

            // If that fails, try the older function name
            if (!$response || !isset($response['loginurl'])) {
                try {
                    $response = $this->call('auth_userkey_generatekey', $params);
                    $functionName = 'auth_userkey_generatekey';
                } catch (\Exception $e) {
                    Log::warning('auth_userkey_generatekey also failed', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if (isset($response['loginurl'])) {
                $loginUrl = $response['loginurl'];

                // Append redirect URL if provided
                if ($redirectUrl) {
                    $separator = (strpos($loginUrl, '?') !== false) ? '&' : '?';
                    $loginUrl .= $separator . 'wantsurl=' . urlencode($redirectUrl);
                }

                Log::info('Generated Moodle auto-login URL', [
                    'username' => $userData['username'] ?? 'N/A',
                    'email' => $userData['email'] ?? 'N/A',
                    'function' => $functionName,
                    'has_redirect' => !empty($redirectUrl)
                ]);

                return $loginUrl;
            }

            return null;
        } catch (\Exception $e) {
            // If auth_userkey is not installed, try fallback method
            Log::warning('auth_userkey not available, falling back to direct URL', [
                'error' => $e->getMessage(),
                'email' => $userData['email'] ?? 'N/A'
            ]);

            return $this->generateFallbackLoginUrl($userData['email'] ?? '', $redirectUrl);
        }
    }

    /**
     * Fallback method to generate a Moodle URL without SSO
     * This simply constructs the course URL - user will need to login manually
     *
     * @param string $email User's email
     * @param string|null $redirectUrl The target URL in Moodle
     * @return string|null The Moodle URL
     */
    protected function generateFallbackLoginUrl(string $email, ?string $redirectUrl = null): ?string
    {
        // If no redirect URL, just return the Moodle base URL
        if (!$redirectUrl) {
            return $this->baseUrl;
        }

        // Return the redirect URL directly - user will need to login
        return $redirectUrl;
    }

    /**
     * Get Moodle user by email
     *
     * @param string $email
     * @return array|null User data or null if not found
     */
    public function getUserByEmail(string $email): ?array
    {
        try {
            $response = $this->call('core_user_get_users', [
                'criteria' => [
                    [
                        'key' => 'email',
                        'value' => $email
                    ]
                ]
            ]);

            if (isset($response['users']) && count($response['users']) > 0) {
                return $response['users'][0];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get Moodle user by email', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);
            return null;
        }
    }

    /**
     * Build a direct Moodle course URL
     *
     * @param int $moodleCourseId The Moodle course ID
     * @return string The course URL
     */
    public function getCourseUrl(int $moodleCourseId): string
    {
        return $this->baseUrl . '/course/view.php?id=' . $moodleCourseId;
    }

    /**
     * Generate auto-login URL for a course.
     *
     * Calls Moodle's auth_userkey plugin to get a one-time SSO login URL.
     * If SSO fails, throws an exception so the caller can handle it
     * (instead of silently falling back to a guest URL).
     *
     * @param \App\Models\User $user The user object
     * @param int $moodleCourseId Moodle course ID
     * @return string The SSO auto-login URL to the course
     * @throws \App\Exceptions\MoodleException If SSO is not available
     */
    public function generateCourseLoginUrl($user, int $moodleCourseId): string
    {
        $courseUrl = $this->getCourseUrl($moodleCourseId);

        // Build user data array with all fields needed for auth_userkey
        $username = $user->username ?? explode('@', $user->email)[0];

        $userData = [
            'username' => $username,
            'email' => $user->email,
            'firstname' => $user->first_name ?? $user->firstname ?? explode('.', $username)[0] ?? 'User',
            'lastname' => $user->last_name ?? $user->lastname ?? explode('.', $username)[1] ?? 'User',
        ];

        $loginUrl = $this->generateLoginUrl($userData, $courseUrl);

        // Verify we got an actual SSO URL, not just the raw course URL back.
        // If generateLoginUrl() returned null or the same course URL, SSO failed.
        if ($loginUrl && $loginUrl !== $courseUrl && $loginUrl !== $this->baseUrl) {
            return $loginUrl;
        }

        // SSO failed — throw so the caller shows a proper error instead of
        // redirecting the user to Moodle as an unauthenticated guest.
        throw new \App\Exceptions\MoodleException(
            "Moodle SSO is not available. A Moodle administrator must: "
            . "(1) Install and enable the auth_userkey plugin, "
            . "(2) Add 'auth_userkey_request_login_url' to the external service's allowed functions "
            . "under Site Administration > Server > Web services > External services."
        );
    }
}