<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\MoodleClient;
use App\Exceptions\MoodleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    private MoodleClient $moodleClient;

    public function __construct(MoodleClient $moodleClient)
    {
        $this->moodleClient = $moodleClient;
    }

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
            'image'       => 'nullable|image|max:2048',
            // Optional Moodle fields
            'sync_to_moodle' => 'nullable|boolean',
            'moodle_course_shortname' => 'nullable|required_if:sync_to_moodle,true|string|max:255|unique:courses,moodle_course_shortname',
            'moodle_category_id' => 'nullable|required_if:sync_to_moodle,true|integer',
        ]);

        if ($request->hasFile('image')){
            $path = $request->file('image')->store('courses','public');
            $validated['image'] = $path;
        }

        DB::beginTransaction();
        try {
            // Create the course locally
            $course = Course::create($validated);

            // Sync to Moodle if requested
            if ($request->boolean('sync_to_moodle')) {
                $moodleCourseId = $this->createMoodleCourse($course, $validated);
                $course->update(['moodle_course_id' => $moodleCourseId]);
            }

            DB::commit();
            
            $message = $course->moodle_course_id 
                ? 'Course created successfully and synced to Moodle.'
                : 'Course created successfully.';
                
            return redirect()->route('courses.index')->with('success', $message);
            
        } catch (MoodleException $e) {
            DB::rollBack();
            Log::error('Failed to create Moodle course', [
                'error' => $e->getMessage(),
                'course_title' => $validated['title']
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Course created locally but failed to sync to Moodle: ' . $e->getMessage());
        }
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
        $course = Course::findOrFail($id);
        
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'status'      => 'required|string|max:255',
            'image'       => 'nullable|image|max:2048',
            'sync_to_moodle' => 'nullable|boolean',
            'moodle_course_shortname' => 'nullable|string|max:255|unique:courses,moodle_course_shortname,' . $id,
            'moodle_category_id' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')){
            $path = $request->file('image')->store('courses','public');
            $validated['image'] = $path;
        }

        DB::beginTransaction();
        try {
            // Update local course
            $course->update($validated);

            // Handle Moodle sync
            if ($request->boolean('sync_to_moodle')) {
                if ($course->moodle_course_id) {
                    // Update existing Moodle course
                    $this->updateMoodleCourse($course);
                } else {
                    // Create new Moodle course
                    $moodleCourseId = $this->createMoodleCourse($course, $validated);
                    $course->update(['moodle_course_id' => $moodleCourseId]);
                }
            }

            DB::commit();
            
            return redirect()->route('courses.index')
                ->with('success', 'Course updated successfully.');
                
        } catch (MoodleException $e) {
            DB::rollBack();
            Log::error('Failed to update Moodle course', [
                'error' => $e->getMessage(),
                'course_id' => $course->id
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Course updated locally but Moodle sync failed: ' . $e->getMessage());
        }
    }

    // Remove the specified course from the database
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        
        // Note: We don't delete from Moodle automatically for safety
        // You may want to add a warning if course exists in Moodle
        if ($course->moodle_course_id) {
            Log::warning('Deleting course that exists in Moodle', [
                'course_id' => $course->id,
                'moodle_course_id' => $course->moodle_course_id
            ]);
        }
        
        $course->delete();

        return redirect()->route('courses.index')
        ->with('success', 'Course deleted successfully from local system.');
    }

    /**
     * Create a course in Moodle
     */
    private function createMoodleCourse(Course $course, array $validated): int
    {
        $moodleData = [
            'courses' => [
                [
                    'fullname' => $course->title,
                    'shortname' => $validated['moodle_course_shortname'] ?? 'course_' . $course->id,
                    'categoryid' => $validated['moodle_category_id'] ?? 10, // Default to Miscellaneous
                    'summary' => strip_tags($course->description),
                    'summaryformat' => 1, // HTML format
                    'format' => 'topics', // Course format
                    'showgrades' => 1,
                    'newsitems' => 5,
                    'startdate' => time(),
                    'enddate' => 0, // No end date
                    'visible' => $course->status === 'active' ? 1 : 0,
                ]
            ]
        ];

        $response = $this->moodleClient->call('core_course_create_courses', $moodleData);

        if (!isset($response[0]['id'])) {
            throw new MoodleException('Failed to create Moodle course: Invalid response');
        }

        return (int) $response[0]['id'];
    }

    /**
     * Update a course in Moodle
     */
    private function updateMoodleCourse(Course $course): void
    {
        $moodleData = [
            'courses' => [
                [
                    'id' => $course->moodle_course_id,
                    'fullname' => $course->title,
                    'summary' => strip_tags($course->description),
                    'visible' => $course->status === 'active' ? 1 : 0,
                ]
            ]
        ];

        $this->moodleClient->call('core_course_update_courses', $moodleData);
    }

    /**
     * Sync course to Moodle (admin action)
     */
    public function syncToMoodle(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        if ($course->moodle_course_id) {
            return redirect()->back()
                ->with('info', 'Course is already synced to Moodle.');
        }

        $request->validate([
            'moodle_course_shortname' => 'required|string|max:255|unique:courses,moodle_course_shortname',
            'moodle_category_id' => 'required|integer',
        ]);

        try {
            $moodleCourseId = $this->createMoodleCourse($course, $request->all());
            
            $course->update([
                'moodle_course_id' => $moodleCourseId,
                'moodle_course_shortname' => $request->moodle_course_shortname,
            ]);

            return redirect()->back()
                ->with('success', 'Course synced to Moodle successfully.');
                
        } catch (MoodleException $e) {
            return redirect()->back()
                ->with('error', 'Failed to sync course to Moodle: ' . $e->getMessage());
        }
    }
}