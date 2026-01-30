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
        // Check for both possible variable names
        $this->baseUrl = config('services.moodle.url', env('MOODLE_URL', env('MOODLE_BASE_URL')));
        $this->token = config('services.moodle.token', env('MOODLE_TOKEN'));
        
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
     * @param string $email User's email address (must match Moodle account)
     * @param string|null $redirectUrl Optional URL to redirect after login (e.g., course page)
     * @return string|null The auto-login URL or null on failure
     */
    public function generateLoginUrl(string $email, ?string $redirectUrl = null): ?string
    {
        try {
            // Call Moodle's auth_userkey_request_login_url function
            // This requires the auth_userkey plugin to be installed and configured
            $params = ['user' => ['email' => $email]];

            $response = $this->call('auth_userkey_request_login_url', $params);

            if (isset($response['loginurl'])) {
                $loginUrl = $response['loginurl'];

                // Append redirect URL if provided
                if ($redirectUrl) {
                    $loginUrl .= '&wantsurl=' . urlencode($redirectUrl);
                }

                Log::info('Generated Moodle auto-login URL', [
                    'email' => $email,
                    'has_redirect' => !empty($redirectUrl)
                ]);

                return $loginUrl;
            }

            return null;
        } catch (\Exception $e) {
            // If auth_userkey is not installed, try fallback method
            Log::warning('auth_userkey not available, falling back to direct URL', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);

            return $this->generateFallbackLoginUrl($email, $redirectUrl);
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
     * Generate auto-login URL for a course
     *
     * @param string $email User's email
     * @param int $moodleCourseId Moodle course ID
     * @return string The auto-login URL to the course
     */
    public function generateCourseLoginUrl(string $email, int $moodleCourseId): string
    {
        $courseUrl = $this->getCourseUrl($moodleCourseId);
        $loginUrl = $this->generateLoginUrl($email, $courseUrl);

        // If SSO failed, return the direct course URL
        return $loginUrl ?? $courseUrl;
    }
}