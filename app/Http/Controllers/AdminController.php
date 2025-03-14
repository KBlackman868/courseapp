<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Notifications\EnrollmentApprovedNotification;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // List pending enrollment requests
    public function enrollmentRequests()
    {
        $enrollments = Enrollment::where('status', 'pending')->with('user', 'course')->get();
        return view('admin.enrollments.index', compact('enrollments'));
    }

    // Approve an enrollment request
    public function approveEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->status = 'approved';
        $enrollment->save();

        // Notify the course creator (this example assumes each course has a 'creator' relationship)
        $courseCreator = $enrollment->course->creator;
        $courseCreator->notify(new EnrollmentApprovedNotification($enrollment));

        // Notify the enrolled user about the approval
        $enrollment->user->notify(new EnrollmentApprovedNotification($enrollment));

        return redirect()->back()->with('success', 'Enrollment approved.');
    }


    // Optionally, add a method for denying enrollment
    public function denyEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->status = 'denied';
        $enrollment->save();

        // Notify the user of denial if needed.
        // $enrollment->user->notify(new EnrollmentDeniedNotification($enrollment));

        return redirect()->back()->with('success', 'Enrollment denied.');
    }
}
