<?php


namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // Show list of courses
    public function index()
    {
        $courses = Course::all();
        return view('courses.index', compact('courses'));
    }

    // Show details for a single course
    public function show($courseId)
    {
        $course = Course::findOrFail($courseId);
        return view('courses.show', compact('course'));
    }
}

