<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Jobs\EnrollUserIntoMoodleCourse;
use App\Notifications\EnrollmentApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // List pending enrollment requests with server-side pagination
    public function enrollmentRequests(Request $request)
    {
        $perPage = $request->query('per_page', 20);

        $enrollments = Enrollment::where('status', 'pending')
            ->with('user', 'course')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('admin.enrollments.index', compact('enrollments'));
    }

    // Approve an enrollment request
    public function approveEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->status = 'approved';
        $enrollment->save();

        // Notify the course creator if exists
        if ($enrollment->course->creator) {
            try {
                $enrollment->course->creator->notify(new EnrollmentApprovedNotification($enrollment));
            } catch (\Exception $e) {
                Log::error('Failed to notify course creator', [
                    'enrollment_id' => $enrollment->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Notify the enrolled user about the approval
        try {
            $enrollment->user->notify(new EnrollmentApprovedNotification($enrollment));
        } catch (\Exception $e) {
            Log::error('Failed to notify enrolled user', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage()
            ]);
        }

        // Sync to Moodle if configured
        if ($enrollment->course->moodle_course_id && $enrollment->user->moodle_user_id) {
            EnrollUserIntoMoodleCourse::dispatch(
                $enrollment->user,
                $enrollment->course->moodle_course_id
            );
        } elseif ($enrollment->course->moodle_course_id) {
            // Create Moodle user first, then enroll
            CreateOrLinkMoodleUser::dispatch($enrollment->user);
        }

        return redirect()->back()->with('success', 'Enrollment approved.');
    }

    // Optionally, add a method for denying enrollment
    public function denyEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->status = 'denied';
        $enrollment->save();

        return redirect()->back()->with('success', 'Enrollment denied.');
    }
}