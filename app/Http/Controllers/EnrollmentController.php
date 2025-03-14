<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\NewCourseEnrollmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;


class EnrollmentController extends Controller
{
    // Store an enrollment request
    public function store(Request $request, $courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        // Create the enrollment record (status pending)
        $enrollment = Enrollment::create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'status'    => 'pending',
        ]);

        // Retrieve the Moodle Administrator(s) (this might be based on a role)
        $admin = User::where('email', 'kyle.blackman@health.gov.tt')->first();
        if ($admin) {
            Notification::send($admin, new NewCourseEnrollmentNotification($enrollment));
        }


        // Send notification to the administrator(s)
        //Notification::send($admins, new NewCourseEnrollmentNotification($enrollment));

        return redirect()->back()->with('success', 'Your enrollment request has been submitted.');
    }
}
