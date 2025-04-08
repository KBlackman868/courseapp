<?php

namespace App\Http\Controllers;

use App\Mail\NewCourseEnrollmentEmail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\NewCourseEnrollmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    public function store(Request $request, Course $course)
{
    $user = Auth::user();

    // Check if the user is already enrolled in this course
    $existingEnrollment = Enrollment::where('user_id', $user->id)
        ->where('course_id', $course->id)
        ->whereIn('status', ['pending', 'approved'])
        ->first();

    if ($existingEnrollment) {
        return redirect()->back()->with('error', 'You are already enrolled in this course.');
    }

    // Create the enrollment record with status pending
    $enrollment = Enrollment::create([
        'user_id'   => $user->id,
        'course_id' => $course->id,
        'status'    => 'pending',
    ]);

    // Send notification email to superadmins
    $superadmins = User::role('superadmin')->get();
    foreach ($superadmins as $admin) {
        Mail::to($admin->email)->send(new NewCourseEnrollmentEmail($enrollment));
    }

    return redirect()->route('home')->with('success', 'Your enrollment request has been submitted.');
    }

    public function index(Request $request)
    {

        // Get the 'status' query parameter, defaulting to 'pending'
        $status = $request->query('status', 'pending');
        // Retrieve enrollments with the given status along with user and course data
            $enrollments = Enrollment::where('status', $status)
                                    ->with('user', 'course')
                                    ->get();

        // Retrieve all users
        $users = User::all();
        return view('admin.approval_lists', compact('enrollments', 'status', 'users'));
    }

    public function update(Request $request, $enrollmentId)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,denied',
        ]);

        $enrollment = Enrollment::findOrFail($enrollmentId);
        $enrollment->status = $request->status;
        $enrollment->save();

        return redirect()->back()->with('success', 'Enrollment status updated successfully.');
    }
    // view the list of courses when enrolled
    public function myCourses()
{
    $user = Auth::user();
    // Retrieve enrollments for the current user where status is approved.
        $enrollments = Enrollment::where('user_id', $user->id)
                                ->where('status', 'approved')
                                ->with('course')
                                ->get();

    return view('courses.mycourses', compact('enrollments'));
}
    //Unenroll users
    public function unenroll($enrollmentId)
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        // Option 1: Update status to "cancelled"
        $enrollment->status = 'cancelled';
        $enrollment->save();

        // Option 2: Delete the enrollment record
        // $enrollment->delete();

        return redirect()->route('home')->with('success', 'Successfully removed from enrollment.');
    }

}
