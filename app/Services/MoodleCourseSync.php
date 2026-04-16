<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\MoodleClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MoodleCourseSync
{
    private MoodleClient $moodleClient;
    
    public function __construct(MoodleClient $moodleClient)
    {
        $this->moodleClient = $moodleClient;
    }
    
    /**
     * Fetch all courses from Moodle
     */
    public function fetchMoodleCourses(): array
    {
        try {
            // Get all courses from Moodle
            $response = $this->moodleClient->call('core_course_get_courses');
            
            // Filter out site course (usually id = 1)
            return array_filter($response, function($course) {
                return $course['id'] > 1;
            });
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch courses from Moodle', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get courses by category from Moodle
     */
    public function fetchCoursesByCategory(int $categoryId = 0): array
    {
        try {
            $params = [];
            if ($categoryId > 0) {
                $params['options'] = [
                    'ids' => [], // Empty means all courses
                    'categoryid' => $categoryId
                ];
            }
            
            $response = $this->moodleClient->call('core_course_get_courses_by_field', $params);
            
            return $response['courses'] ?? [];
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch courses by category', [
                'category_id' => $categoryId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Sync a single Moodle course to local database
     */
    public function syncCourse(array $moodleCourse): Course
    {
        // Check if course already exists by moodle_course_id
        $course = Course::where('moodle_course_id', $moodleCourse['id'])->first();
        
        if ($course) {
            // Update existing course
            $course->update($this->mapMoodleCourseToLocal($moodleCourse));
            Log::info('Updated existing course from Moodle', [
                'course_id' => $course->id,
                'moodle_id' => $moodleCourse['id']
            ]);
        } else {
            // Create new course
            $courseData = $this->mapMoodleCourseToLocal($moodleCourse);
            $courseData['moodle_course_id'] = $moodleCourse['id'];
            $courseData['moodle_course_shortname'] = $moodleCourse['shortname'];
            
            $course = Course::create($courseData);
            Log::info('Created new course from Moodle', [
                'course_id' => $course->id,
                'moodle_id' => $moodleCourse['id']
            ]);
        }
        
        return $course;
    }
    
    /**
     * Bulk sync all courses from Moodle
     */
    public function syncAllCourses(): array
    {
        $stats = [
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        DB::beginTransaction();
        
        try {
            $moodleCourses = $this->fetchMoodleCourses();
            $stats['total'] = count($moodleCourses);
            
            foreach ($moodleCourses as $moodleCourse) {
                try {
                    $existingCourse = Course::where('moodle_course_id', $moodleCourse['id'])->exists();
                    
                    $this->syncCourse($moodleCourse);
                    
                    if ($existingCourse) {
                        $stats['updated']++;
                    } else {
                        $stats['created']++;
                    }
                    
                } catch (\Exception $e) {
                    $stats['failed']++;
                    $stats['errors'][] = [
                        'course' => $moodleCourse['fullname'] ?? 'Unknown',
                        'moodle_id' => $moodleCourse['id'],
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error('Failed to sync individual course', [
                        'moodle_id' => $moodleCourse['id'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
        return $stats;
    }
    
    /**
     * Fetch the list of courses a Moodle user is enrolled in.
     * Uses core_enrol_get_users_courses. Returns [] on API failure.
     */
    public function fetchUserCourses(int $moodleUserId): array
    {
        try {
            $response = $this->moodleClient->call('core_enrol_get_users_courses', [
                'userid' => $moodleUserId,
            ]);

            return is_array($response) ? $response : [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch user courses from Moodle', [
                'moodle_user_id' => $moodleUserId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Import a user's existing Moodle enrollments into the local registrations table.
     *
     * For each course the user is enrolled in on Moodle:
     *  - match to a local Course via moodle_course_id
     *  - respect audience_type (MOH_ONLY / EXTERNAL_ONLY / BOTH)
     *  - skip if an enrollment already exists (any status)
     *  - otherwise create an approved Enrollment
     *
     * Idempotent. Returns a stats array suitable for flashing back to the UI.
     */
    public function importUserEnrollments(User $user): array
    {
        $stats = [
            'imported' => 0,
            'already_enrolled' => 0,
            'skipped_unknown' => 0,
            'skipped_audience' => 0,
            'imported_titles' => [],
        ];

        if (!$user->moodle_user_id) {
            throw new \RuntimeException('User is not linked to a Moodle account.');
        }

        $moodleCourses = $this->fetchUserCourses((int) $user->moodle_user_id);

        if (empty($moodleCourses)) {
            return $stats;
        }

        // Pre-load local courses keyed by moodle_course_id for one query
        $moodleCourseIds = array_map(fn($c) => $c['id'] ?? null, $moodleCourses);
        $localCourses = Course::whereIn('moodle_course_id', array_filter($moodleCourseIds))
            ->get()
            ->keyBy('moodle_course_id');

        foreach ($moodleCourses as $moodleCourse) {
            $moodleCourseId = $moodleCourse['id'] ?? null;
            if (!$moodleCourseId) {
                continue;
            }

            // Skip the Moodle site course (id=1)
            if ((int) $moodleCourseId === 1) {
                continue;
            }

            $course = $localCourses->get($moodleCourseId);

            if (!$course) {
                $stats['skipped_unknown']++;
                continue;
            }

            if (!$this->courseMatchesUserAudience($course, $user)) {
                $stats['skipped_audience']++;
                continue;
            }

            $existing = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existing) {
                $stats['already_enrolled']++;
                continue;
            }

            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'approved',
            ]);

            $stats['imported']++;
            $stats['imported_titles'][] = $course->title;
        }

        Log::info('Imported Moodle enrollments for user', [
            'user_id' => $user->id,
            'moodle_user_id' => $user->moodle_user_id,
            'stats' => array_diff_key($stats, ['imported_titles' => null]),
        ]);

        return $stats;
    }

    /**
     * Check whether a course's audience_type allows this user to enroll.
     * Mirrors Course::scopeForUser logic without hitting the DB.
     */
    private function courseMatchesUserAudience(Course $course, User $user): bool
    {
        $mohAudiences = [
            Course::AUDIENCE_MOH_ONLY,
            Course::AUDIENCE_MOH,
            Course::AUDIENCE_BOTH,
            Course::AUDIENCE_ALL,
        ];

        $externalAudiences = [
            Course::AUDIENCE_EXTERNAL_ONLY,
            Course::AUDIENCE_EXTERNAL,
            Course::AUDIENCE_BOTH,
            Course::AUDIENCE_ALL,
        ];

        return $user->isInternal()
            ? in_array($course->audience_type, $mohAudiences, true)
            : in_array($course->audience_type, $externalAudiences, true);
    }

    /**
     * Get course enrollments from Moodle
     */
    public function fetchCourseEnrollments(int $moodleCourseId): array
    {
        try {
            $response = $this->moodleClient->call('core_enrol_get_enrolled_users', [
                'courseid' => $moodleCourseId
            ]);
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch course enrollments', [
                'moodle_course_id' => $moodleCourseId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Get course categories from Moodle
     */
    public function fetchCategories(): array
    {
        try {
            return $this->moodleClient->call('core_course_get_categories');
        } catch (\Exception $e) {
            Log::error('Failed to fetch categories', ['error' => $e->getMessage()]);
            return [];
        }
    }
    
    /**
     * Map Moodle course data to local course structure
     */
    private function mapMoodleCourseToLocal(array $moodleCourse): array
    {
        // Clean HTML from summary
        $description = strip_tags($moodleCourse['summary'] ?? '');
        if (empty($description)) {
            $description = 'No description available';
        }
        
        // Determine status based on visibility
        $status = ($moodleCourse['visible'] ?? 1) == 1 ? 'active' : 'inactive';
        
        return [
            'title' => $moodleCourse['fullname'] ?? 'Untitled Course',
            'description' => $description,
            'status' => $status,
            // Note: image will need to be handled separately if needed
        ];
    }
    
    /**
     * Export Moodle courses to array (for Excel export)
     */
    public function exportCoursesToArray(): array
    {
        $courses = $this->fetchMoodleCourses();
        $exportData = [];
        
        foreach ($courses as $course) {
            $enrollments = $this->fetchCourseEnrollments($course['id']);
            
            $exportData[] = [
                'moodle_id' => $course['id'],
                'shortname' => $course['shortname'] ?? '',
                'fullname' => $course['fullname'] ?? '',
                'category' => $course['categoryid'] ?? '',
                'summary' => strip_tags($course['summary'] ?? ''),
                'visible' => $course['visible'] ?? 1,
                'startdate' => isset($course['startdate']) ? date('Y-m-d', $course['startdate']) : '',
                'enddate' => isset($course['enddate']) && $course['enddate'] > 0 ? date('Y-m-d', $course['enddate']) : '',
                'enrolled_users' => count($enrollments),
                'format' => $course['format'] ?? 'topics',
            ];
        }
        
        return $exportData;
    }
}