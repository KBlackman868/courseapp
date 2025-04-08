<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // Display a list of all courses
    public function index()
    {
        $courses = Course::all();
        return view('courses.index', compact('courses'));
    }

    // Display details for a single course
    public function show($id)
    {
        $course = Course::findOrFail($id);
        return view('courses.show', compact('course'));
    }

    // Display the registration page for a specific course (GET)
    public function register($id)
    {
        $course = Course::findOrFail($id);
        return view('courses.register', compact('course'));
    }

    // Display the form to create a new course
    public function create()
    {
        return view('courses.create');
    }

    // Store the new course data in the database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'status'      => 'required|string|max:255',
        ]);

        // Create a new course; the database auto-assigns the course ID.
        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    // Display the form to edit an existing course
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('courses.edit', compact('course'));
    }

    // Update the specified course in the database
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'status'      => 'required|string|max:255',
        ]);

        $course = Course::findOrFail($id);
        $course->update($validated);

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }

    // Remove the specified course from the database
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }
}
