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
        $this->baseUrl = config('services.moodle.url', env('MOODLE_URL'));
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
            // ALWAYS disable SSL verification for self-signed certificates
            $response = Http::withoutVerifying()
                ->asForm()
                ->post($url, $requestParams);
            
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
                'error' => $e->getMessage()
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
                Log::info('Moodle user created successfully', [
                    'moodle_user_id' => $response[0]['id'],
                    'username' => $userData['username']
                ]);
                return $response[0]['id'];
            }

            // Check for username already exists error
            if (isset($response[0]['username'])) {
                // Try to get existing user
                $existingUser = $this->getUserByUsername($userData['username']);
                if ($existingUser) {
                    Log::info('Moodle user already exists', [
                        'moodle_user_id' => $existingUser['id'],
                        'username' => $userData['username']
                    ]);
                    return $existingUser['id'];
                }
            }

            Log::warning('Moodle user creation returned unexpected response', [
                'response' => $response
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Failed to create Moodle user', [
                'error' => $e->getMessage(),
                'username' => $userData['username'] ?? 'unknown'
            ]);
            return null;
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
}