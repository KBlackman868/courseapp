<?php

namespace App\Services;

use App\Models\Course;
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