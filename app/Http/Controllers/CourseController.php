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
        // Server-side pagination - get 6 courses per page
        $courses = Course::where('status', 'active')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10); 
        
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
     * Bulk delete courses
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id'
        ]);

        try {
            $deletedCount = 0;
            $failedCount = 0;
            
            foreach ($request->course_ids as $courseId) {
                $course = Course::find($courseId);
                
                // Check if course has enrollments
                if ($course->enrollments()->exists()) {
                    // Option 1: Skip deletion if has enrollments
                    $failedCount++;
                    continue;
                    
                    // Option 2: Delete enrollments first (uncomment if preferred)
                    // $course->enrollments()->delete();
                }
                
                // Delete the course
                if ($course->delete()) {
                    $deletedCount++;
                    
                    // Log the deletion
                    Log::info('Course deleted', [
                        'course_id' => $courseId,
                        'title' => $course->title,
                        'deleted_by' => auth()->id(),
                        'moodle_course_id' => $course->moodle_course_id
                    ]);
                } else {
                    $failedCount++;
                }
            }
            
            $message = "Deleted {$deletedCount} courses successfully.";
            if ($failedCount > 0) {
                $message .= " Failed to delete {$failedCount} courses (may have active enrollments).";
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Bulk delete courses failed', [
                'error' => $e->getMessage(),
                'course_ids' => $request->course_ids
            ]);
            
            return redirect()->back()->with('error', 'Failed to delete courses: ' . $e->getMessage());
        }
    }

    /**
     * Admin course management view
     */
    /**
     * Admin course management view
     */
    public function adminIndex(Request $request)
    {
        $query = Course::with(['enrollments', 'category']);
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('moodle_course_shortname', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by sync status
        if ($request->has('sync_status')) {
            if ($request->sync_status === 'synced') {
                $query->whereNotNull('moodle_course_id');
            } elseif ($request->sync_status === 'not_synced') {
                $query->whereNull('moodle_course_id');
            }
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // IMPORTANT: Use paginate() not get()
        $courses = $query->paginate(20)->withQueryString();
        
        // Get statistics
        $stats = [
            'total' => Course::count(),
            'active' => Course::where('status', 'active')->count(),
            'inactive' => Course::where('status', 'inactive')->count(),
            'synced' => Course::whereNotNull('moodle_course_id')->count(),
            'not_synced' => Course::whereNull('moodle_course_id')->count(),
        ];
        
        return view('admin.courses.index', compact('courses', 'stats'));
    }
        /**
     * Bulk update course status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $updated = Course::whereIn('id', $request->course_ids)
                ->update(['status' => $request->status]);
            
            return redirect()->back()->with('success', "Updated status for {$updated} courses.");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update course status: ' . $e->getMessage());
        }
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