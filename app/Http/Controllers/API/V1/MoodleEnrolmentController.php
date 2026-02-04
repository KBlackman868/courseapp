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

        // Resolve the Course model - either by local course_id or by moodle_course_id
        $course = null;
        if ($request->course_id) {
            $course = Course::findOrFail($request->course_id);
        } elseif ($request->moodle_course_id) {
            $course = Course::where('moodle_course_id', $request->moodle_course_id)->first();
        }

        if (!$course || !$course->moodle_course_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to determine Moodle course. Provide a valid course_id or moodle_course_id.',
            ], 422);
        }

        // Job expects (User, Course, ?CourseAccessRequest, ?int roleId)
        EnrollUserIntoMoodleCourse::dispatch(
            $user,
            $course,
            null,
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