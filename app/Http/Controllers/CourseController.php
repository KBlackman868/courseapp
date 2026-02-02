<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\MoodleClient;
use App\Services\ActivityLogger;
use App\Exceptions\MoodleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Enrollment;

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
        $courses = Course::with('enrollments')->paginate(12);
        
        // Log viewing courses
        ActivityLogger::logSystem('courses_viewed',
            "User viewed course listing",
            ['page' => request()->get('page', 1)]
        );
        
        return view('courses.index', compact('courses'));
    }
    
    // Display details for a single course
    public function show($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        // Get user's enrollment status for this course
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        // Determine access level based on user type
        $accessLevel = $this->determineAccessLevel($user, $course, $enrollment);

        // Log course view
        ActivityLogger::logCourse('viewed', $course,
            "User viewed course details: {$course->title}",
            [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'enrollment_status' => $enrollment?->status
            ]
        );

        return view('courses.show', compact('course', 'enrollment', 'accessLevel'));
    }

    /**
     * Determine course access level for a user
     * Returns: 'direct_access' | 'enrolled' | 'pending' | 'request_access' | 'enroll' | 'denied'
     */
    private function determineAccessLevel($user, Course $course, ?Enrollment $enrollment): array
    {
        $isInternal = $user->isInternal();
        $hasMoodleSync = $course->moodle_course_id !== null;

        // Check enrollment status first
        if ($enrollment) {
            switch ($enrollment->status) {
                case 'approved':
                    return [
                        'level' => 'enrolled',
                        'can_access_moodle' => $hasMoodleSync && $user->moodle_user_id,
                        'message' => 'You are enrolled in this course.',
                        'action' => $hasMoodleSync ? 'access_course' : 'view_content'
                    ];
                case 'pending':
                    return [
                        'level' => 'pending',
                        'can_access_moodle' => false,
                        'message' => 'Your enrollment request is pending approval.',
                        'action' => 'wait'
                    ];
                case 'denied':
                    return [
                        'level' => 'denied',
                        'can_access_moodle' => false,
                        'message' => 'Your enrollment request was not approved. Contact the course administrator.',
                        'action' => 'contact'
                    ];
            }
        }

        // No enrollment - determine action based on user type
        if ($isInternal) {
            // MOH staff: Direct enrollment path
            return [
                'level' => 'enroll',
                'can_access_moodle' => false,
                'message' => 'As Ministry of Health staff, you can enroll directly.',
                'action' => 'enroll_direct',
                'button_text' => 'Enroll Now',
                'button_style' => 'primary'
            ];
        } else {
            // External users: Request-approval path
            return [
                'level' => 'request_access',
                'can_access_moodle' => false,
                'message' => 'External users require approval to access this course.',
                'action' => 'request_access',
                'button_text' => 'Request Access',
                'button_style' => 'secondary'
            ];
        }
    }

    /**
     * Direct access to Moodle course for enrolled users
     * MOH users with approved enrollment are redirected directly to Moodle via SSO
     */
    public function accessMoodle($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        // Verify user has approved enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            ActivityLogger::logCourse('access_denied', $course,
                "User attempted to access Moodle course without enrollment",
                ['user_id' => $user->id, 'user_email' => $user->email]
            );

            return redirect()->route('courses.show', $course)
                ->with('error', 'You must be enrolled in this course to access it.');
        }

        // Verify course has Moodle integration
        if (!$course->moodle_course_id) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'This course is not yet available in Moodle.');
        }

        // Ensure user has Moodle account
        if (!$user->moodle_user_id) {
            // Trigger Moodle user creation if needed
            \App\Jobs\CreateOrLinkMoodleUser::dispatchSync($user);
            $user->refresh();

            if (!$user->moodle_user_id) {
                return redirect()->route('courses.show', $course)
                    ->with('info', 'Your Moodle account is being set up. Please try again in a few moments.');
            }
        }

        // Log Moodle access
        ActivityLogger::logMoodle('course_accessed',
            "User accessed Moodle course: {$course->title}",
            $course,
            [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_type' => $user->user_type,
                'moodle_user_id' => $user->moodle_user_id
            ]
        );

        // Build Moodle URL with SSO
        $moodleBaseUrl = config('moodle.base_url', 'https://learnabouthealth.hin.gov.tt');

        // Check if SSO is enabled
        if (config('moodle.sso_enabled') && config('moodle.sso_secret')) {
            // Use auth_userkey plugin for SSO
            $moodleUrl = $this->buildMoodleSSOUrl($user, $course, $moodleBaseUrl);
        } else {
            // Fallback to direct URL (will require Moodle login)
            $moodleUrl = $moodleBaseUrl . '/course/view.php?id=' . $course->moodle_course_id;
            Log::warning('Moodle SSO not configured, user will need to login manually', [
                'user_id' => $user->id,
                'course_id' => $course->id
            ]);
        }

        return redirect()->away($moodleUrl);
    }

    /**
     * Build Moodle SSO URL using auth_userkey plugin
     * Calls Moodle's auth_userkey_request_login_url web service
     */
    private function buildMoodleSSOUrl($user, Course $course, string $moodleBaseUrl): string
    {
        try {
            // Call Moodle API to generate login URL
            // The auth_userkey plugin provides this web service function
            $result = $this->moodleClient->call('auth_userkey_request_login_url', [
                'user' => [
                    'email' => $user->email,
                ],
            ]);

            if (isset($result['loginurl'])) {
                // Append the course redirect URL
                $loginUrl = $result['loginurl'];
                $wantsUrl = $moodleBaseUrl . '/course/view.php?id=' . $course->moodle_course_id;

                // Add wantsurl parameter for redirect after login
                $separator = (strpos($loginUrl, '?') !== false) ? '&' : '?';
                $finalUrl = $loginUrl . $separator . 'wantsurl=' . urlencode($wantsUrl);

                Log::info('Generated Moodle SSO URL via API', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'course_id' => $course->id,
                    'moodle_course_id' => $course->moodle_course_id,
                ]);

                return $finalUrl;
            }

            throw new \Exception('Invalid response from Moodle auth_userkey API');

        } catch (\Exception $e) {
            Log::error('Failed to generate Moodle SSO URL', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            // Fallback to direct URL (user will need to login manually)
            return $moodleBaseUrl . '/course/view.php?id=' . $course->moodle_course_id;
        }
    }

    // Display the registration page for a specific course (GET)
    public function register($id)
    {
        $course = Course::findOrFail($id);

        // Log registration page view
        ActivityLogger::logCourse('registration_viewed', $course,
            "User viewed registration page for: {$course->title}",
            ['user_id' => auth()->id()]
        );

        return view('courses.register', compact('course'));
    }

    // Display the form to create a new course
    public function create()
    {
        // Log admin accessing course creation
        ActivityLogger::logSystem('course_create_accessed',
            "Admin accessed course creation form",
            ['admin' => auth()->user()->email]
        );
        
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

            // Log course creation
            ActivityLogger::logCourse('created', $course,
                "New course created: {$course->title}",
                [
                    'created_by' => auth()->user()->email,
                    'status' => $course->status,
                    'has_image' => isset($validated['image']),
                    'moodle_sync_requested' => $request->boolean('sync_to_moodle')
                ]
            );

            // Sync to Moodle if requested
            if ($request->boolean('sync_to_moodle')) {
                $moodleCourseId = $this->createMoodleCourse($course, $validated);
                $course->update(['moodle_course_id' => $moodleCourseId]);
                
                // Log successful Moodle sync
                ActivityLogger::logMoodle('course_synced', 
                    "Course synced to Moodle: {$course->title}",
                    $course,
                    [
                        'moodle_course_id' => $moodleCourseId,
                        'moodle_shortname' => $validated['moodle_course_shortname'] ?? null,
                        'moodle_category' => $validated['moodle_category_id'] ?? null
                    ]
                );
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
            
            // Log Moodle sync failure
            ActivityLogger::logMoodle('course_sync_failed',
                "Failed to sync course to Moodle: {$validated['title']}",
                null,
                [
                    'error' => $e->getMessage(),
                    'attempted_by' => auth()->user()->email
                ],
                'failed',
                'error'
            );
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Course created locally but failed to sync to Moodle: ' . $e->getMessage());
        }
    }

    // Display the form to edit an existing course
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        
        // Log course edit access
        ActivityLogger::logCourse('edit_accessed', $course,
            "Admin accessed course edit form: {$course->title}",
            ['admin' => auth()->user()->email]
        );
        
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

        // Store old values for logging
        $oldValues = $course->only(['title', 'description', 'status', 'moodle_course_id']);

        if ($request->hasFile('image')){
            $path = $request->file('image')->store('courses','public');
            $validated['image'] = $path;
        }

        DB::beginTransaction();
        try {
            // Update local course
            $course->update($validated);

            // Log course update
            ActivityLogger::logCourse('updated', $course,
                "Course updated: {$course->title}",
                [
                    'updated_by' => auth()->user()->email,
                    'old_values' => $oldValues,
                    'new_values' => $course->only(['title', 'description', 'status', 'moodle_course_id']),
                    'changed_fields' => array_keys($course->getChanges())
                ]
            );

            // Handle Moodle sync
            if ($request->boolean('sync_to_moodle')) {
                if ($course->moodle_course_id) {
                    // Update existing Moodle course
                    $this->updateMoodleCourse($course);
                    
                    // Log Moodle update
                    ActivityLogger::logMoodle('course_updated',
                        "Course updated in Moodle: {$course->title}",
                        $course,
                        ['moodle_course_id' => $course->moodle_course_id]
                    );
                } else {
                    // Create new Moodle course
                    $moodleCourseId = $this->createMoodleCourse($course, $validated);
                    $course->update(['moodle_course_id' => $moodleCourseId]);
                    
                    // Log new Moodle sync
                    ActivityLogger::logMoodle('course_synced',
                        "Course newly synced to Moodle: {$course->title}",
                        $course,
                        ['moodle_course_id' => $moodleCourseId]
                    );
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
            
            // Log Moodle update failure
            ActivityLogger::logMoodle('course_update_failed',
                "Failed to update course in Moodle: {$course->title}",
                $course,
                ['error' => $e->getMessage()],
                'failed',
                'error'
            );
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Course updated locally but Moodle sync failed: ' . $e->getMessage());
        }
    }

    // Remove the specified course from the database
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        
        // Store course data before deletion for logging
        $courseData = [
            'id' => $course->id,
            'title' => $course->title,
            'moodle_course_id' => $course->moodle_course_id,
            'enrollment_count' => $course->enrollments()->count(),
            'had_moodle_sync' => !is_null($course->moodle_course_id),
            'status' => $course->status
        ];
        
        // Note: We don't delete from Moodle automatically for safety
        if ($course->moodle_course_id) {
            Log::warning('Deleting course that exists in Moodle', [
                'course_id' => $course->id,
                'moodle_course_id' => $course->moodle_course_id
            ]);
            
            // Log warning about Moodle course
            ActivityLogger::logCourse('delete_warning', $course,
                "Deleting course that exists in Moodle: {$course->title}",
                [
                    'moodle_course_id' => $course->moodle_course_id,
                    'enrollment_count' => $courseData['enrollment_count']
                ],
                'success',
                'warning'
            );
        }
        
        $course->delete();

        // Log course deletion
        ActivityLogger::logCourse('deleted', null,
            "Course deleted: {$courseData['title']}",
            [
                'deleted_by' => auth()->user()->email,
                'course_data' => $courseData,
                'timestamp' => now()
            ]
        );

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
            $deletedCourses = [];
            $skippedCourses = [];

            // PERFORMANCE FIX: Load all courses at once with enrollment count
            $courses = Course::whereIn('id', $request->course_ids)
                ->withCount('enrollments')
                ->get()
                ->keyBy('id');

            foreach ($request->course_ids as $courseId) {
                $course = $courses->get($courseId);

                if (!$course) {
                    $failedCount++;
                    continue;
                }

                // Check if course has enrollments using pre-loaded count
                if ($course->enrollments_count > 0) {
                    $failedCount++;
                    $skippedCourses[] = $course->title;

                    // Log skipped deletion
                    ActivityLogger::logCourse('delete_skipped', $course,
                        "Course deletion skipped due to active enrollments: {$course->title}",
                        [
                            'enrollment_count' => $course->enrollments_count,
                            'attempted_by' => auth()->user()->email
                        ],
                        'failed',
                        'warning'
                    );
                    continue;
                }

                // Delete the course
                $courseTitle = $course->title;
                if ($course->delete()) {
                    $deletedCount++;
                    $deletedCourses[] = $courseTitle;

                    // Log successful deletion
                    ActivityLogger::logCourse('bulk_deleted', null,
                        "Course deleted in bulk operation: {$courseTitle}",
                        [
                            'deleted_by' => auth()->user()->email,
                            'course_id' => $courseId
                        ]
                    );
                } else {
                    $failedCount++;
                }
            }
            
            // Log bulk operation summary
            ActivityLogger::logSystem('bulk_course_delete',
                "Bulk course deletion completed",
                [
                    'total_selected' => count($request->course_ids),
                    'deleted' => $deletedCount,
                    'failed' => $failedCount,
                    'deleted_courses' => $deletedCourses,
                    'skipped_courses' => $skippedCourses,
                    'performed_by' => auth()->user()->email
                ],
                $failedCount > 0 ? 'partial' : 'success',
                $failedCount > 0 ? 'warning' : 'info'
            );
            
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
            
            // Log bulk operation error
            ActivityLogger::logSystem('bulk_delete_error',
                "Bulk course deletion failed",
                [
                    'error' => $e->getMessage(),
                    'course_ids' => $request->course_ids,
                    'attempted_by' => auth()->user()->email
                ],
                'failed',
                'error'
            );
            
            return redirect()->back()->with('error', 'Failed to delete courses: ' . $e->getMessage());
        }
    }

    /**
     * Toggle course status (active/inactive)
     */
    public function toggleStatus(Request $request, Course $course)
    {
        try {
            $oldStatus = $course->status;
            $newStatus = $course->status === 'active' ? 'inactive' : 'active';
            $course->update(['status' => $newStatus]);
            
            // Log status toggle
            ActivityLogger::logCourse('status_toggled', $course,
                "Course status changed from {$oldStatus} to {$newStatus}: {$course->title}",
                [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'toggled_by' => auth()->user()->email
                ]
            );
            
            // If course is in Moodle, update visibility there too
            if ($course->moodle_course_id) {
                try {
                    $this->updateMoodleCourse($course);
                    Log::info('Course status synced to Moodle', [
                        'course_id' => $course->id,
                        'new_status' => $newStatus
                    ]);
                    
                    // Log Moodle sync
                    ActivityLogger::logMoodle('course_visibility_updated',
                        "Course visibility updated in Moodle",
                        $course,
                        ['visible' => $newStatus === 'active']
                    );
                } catch (MoodleException $e) {
                    Log::error('Failed to sync status to Moodle', [
                        'course_id' => $course->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue even if Moodle sync fails
                }
            }
            
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "Course is now {$newStatus}"
            ]);
            
        } catch (\Exception $e) {
            // Log error
            ActivityLogger::logCourse('status_toggle_failed', $course,
                "Failed to toggle course status: {$course->title}",
                ['error' => $e->getMessage()],
                'failed',
                'error'
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update course status'
            ], 500);
        }
    }

    /**
     * Bulk sync courses to Moodle
     */
    public function bulkSync(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id'
        ]);
        
        $syncedCount = 0;
        $failedCount = 0;
        $errors = [];
        $syncedCourses = [];
        $failedCourses = [];

        // PERFORMANCE FIX: Load all courses at once
        $courses = Course::whereIn('id', $request->course_ids)->get()->keyBy('id');

        foreach ($request->course_ids as $courseId) {
            try {
                $course = $courses->get($courseId);

                if (!$course) {
                    $failedCount++;
                    continue;
                }
                
                if ($course->moodle_course_id) {
                    // Already synced, update it
                    $this->updateMoodleCourse($course);
                    Log::info('Updated existing Moodle course', [
                        'course_id' => $course->id,
                        'moodle_course_id' => $course->moodle_course_id
                    ]);
                    
                    $syncedCourses[] = $course->title;
                } else {
                    // Create new Moodle course
                    $moodleCourseId = $this->createMoodleCourse($course, [
                        'moodle_course_shortname' => 'course_' . $course->id,
                        'moodle_category_id' => 1 // Default category
                    ]);
                    
                    $course->update([
                        'moodle_course_id' => $moodleCourseId,
                        'moodle_course_shortname' => 'course_' . $course->id
                    ]);
                    
                    Log::info('Created new Moodle course', [
                        'course_id' => $course->id,
                        'moodle_course_id' => $moodleCourseId
                    ]);
                    
                    $syncedCourses[] = $course->title;
                }
                
                $syncedCount++;
                
            } catch (MoodleException $e) {
                $failedCount++;
                $failedCourses[] = $course->title ?? "Course ID {$courseId}";
                $errors[] = "Course ID {$courseId}: " . $e->getMessage();
                Log::error('Failed to sync course in bulk operation', [
                    'course_id' => $courseId,
                    'error' => $e->getMessage()
                ]);
            } catch (\Exception $e) {
                $failedCount++;
                $failedCourses[] = $course->title ?? "Course ID {$courseId}";
                $errors[] = "Course ID {$courseId}: Unexpected error";
                Log::error('Unexpected error during bulk sync', [
                    'course_id' => $courseId,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Log bulk sync operation
        ActivityLogger::logMoodle('bulk_course_sync',
            "Bulk course sync to Moodle completed",
            null,
            [
                'total_selected' => count($request->course_ids),
                'synced' => $syncedCount,
                'failed' => $failedCount,
                'synced_courses' => $syncedCourses,
                'failed_courses' => $failedCourses,
                'performed_by' => auth()->user()->email
            ],
            $failedCount > 0 ? 'partial' : 'success',
            $failedCount > 0 ? 'warning' : 'info'
        );
        
        $message = "Synced {$syncedCount} course(s) to Moodle.";
        
        if ($failedCount > 0) {
            $message .= " Failed to sync {$failedCount} course(s).";
            if (!empty($errors)) {
                session()->flash('sync_errors', $errors);
            }
        }
        
        return redirect()->back()->with($syncedCount > 0 ? 'success' : 'error', $message);
    }

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
        
        // PERFORMANCE FIX: Get all statistics in a single query
        $statsRaw = DB::table('courses')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active")
            ->selectRaw("SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive")
            ->selectRaw('SUM(CASE WHEN moodle_course_id IS NOT NULL THEN 1 ELSE 0 END) as synced')
            ->selectRaw('SUM(CASE WHEN moodle_course_id IS NULL THEN 1 ELSE 0 END) as not_synced')
            ->first();

        $stats = [
            'total' => (int) $statsRaw->total,
            'active' => (int) $statsRaw->active,
            'inactive' => (int) $statsRaw->inactive,
            'synced' => (int) $statsRaw->synced,
            'not_synced' => (int) $statsRaw->not_synced,
        ];
        
        // Log admin viewing courses
        ActivityLogger::logSystem('admin_courses_viewed',
            "Admin viewed course management",
            [
                'admin' => auth()->user()->email,
                'filters' => $request->only(['search', 'status', 'sync_status']),
                'page' => $request->get('page', 1)
            ]
        );
        
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
            
            // Log bulk status update
            ActivityLogger::logSystem('bulk_status_update',
                "Bulk course status update to {$request->status}",
                [
                    'course_ids' => $request->course_ids,
                    'new_status' => $request->status,
                    'updated_count' => $updated,
                    'performed_by' => auth()->user()->email
                ]
            );
            
            return redirect()->back()->with('success', "Updated status for {$updated} courses.");
            
        } catch (\Exception $e) {
            // Log error
            ActivityLogger::logSystem('bulk_status_update_failed',
                "Failed to bulk update course status",
                [
                    'error' => $e->getMessage(),
                    'attempted_by' => auth()->user()->email
                ],
                'failed',
                'error'
            );
            
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
            
            // Log manual sync
            ActivityLogger::logMoodle('course_manually_synced',
                "Course manually synced to Moodle: {$course->title}",
                $course,
                [
                    'moodle_course_id' => $moodleCourseId,
                    'synced_by' => auth()->user()->email
                ]
            );

            return redirect()->back()
                ->with('success', 'Course synced to Moodle successfully.');
                
        } catch (MoodleException $e) {
            // Log sync failure
            ActivityLogger::logMoodle('manual_sync_failed',
                "Failed to manually sync course to Moodle: {$course->title}",
                $course,
                ['error' => $e->getMessage()],
                'failed',
                'error'
            );
            
            return redirect()->back()
                ->with('error', 'Failed to sync course to Moodle: ' . $e->getMessage());
        }
    }
}