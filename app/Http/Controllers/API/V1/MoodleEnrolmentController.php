<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMoodleEnrolmentRequest;
use App\Jobs\EnrollUserIntoMoodleCourse;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class MoodleEnrolmentController extends Controller
{
    /**
     * Enroll a user into a Moodle course
     */
    public function store(CreateMoodleEnrolmentRequest $request): JsonResponse
    {
        $user = User::findOrFail($request->user_id);
        
        // Determine Moodle course ID
        $moodleCourseId = $request->moodle_course_id;
        
        if (!$moodleCourseId && $request->course_id) {
            // If using local course_id, resolve to moodle_course_id
            // Assuming you have a courses table with moodle_course_id field
            // $course = Course::findOrFail($request->course_id);
            // $moodleCourseId = $course->moodle_course_id;
            
            // Alternative: Use a mapping service or configuration
            // $moodleCourseId = $this->resolveMoodleCourseId($request->course_id);
            
            // For now, we'll use a simple mapping (you should implement your own logic)
            $moodleCourseId = $request->course_id; // Temporary direct mapping
        }

        if (!$moodleCourseId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to determine Moodle course ID',
            ], 422);
        }

        EnrollUserIntoMoodleCourse::dispatch(
            $user,
            $moodleCourseId,
            $request->role_id
        );

        return response()->json([
            'status' => 'ok',
            'message' => 'Enrollment queued successfully',
        ], 201);
    }
    
    /**
     * Example method for resolving Moodle course ID from local course ID
     * Implement based on your specific requirements
     */
    private function resolveMoodleCourseId(int $localCourseId): ?int
    {
        // Option 1: Database mapping table
        // return DB::table('course_mappings')
        //     ->where('local_course_id', $localCourseId)
        //     ->value('moodle_course_id');
        
        // Option 2: Configuration file mapping
        // $mappings = config('moodle.course_mappings', []);
        // return $mappings[$localCourseId] ?? null;
        
        // Option 3: Direct mapping (if IDs match)
        return $localCourseId;
    }
}