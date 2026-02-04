<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseAccessRequest;
use App\Services\MoodleClient;
use App\Services\ActivityLogger;
use App\Exceptions\MoodleException;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Jobs\EnrollUserIntoMoodleCourse;
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
    public function index(Request $request)
    {
        $query = Course::with('enrollments');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
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

        $query->orderBy('created_at', 'desc');
        $courses = $query->paginate(12)->withQueryString();

        // Statistics
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

        // Log viewing courses
        ActivityLogger::logSystem('courses_viewed',
            "User viewed course listing",
            ['page' => $request->get('page', 1)]
        );

        return view('courses.index', compact('courses', 'stats'));
    }
    
    // Display details for a single course
    public function show($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        // Get user's existing enrollment for this course (any status)
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        // AUTO-ENROLL INTERNAL (MOH) USERS:
        // Internal users get immediate approved enrollment without clicking any button.
        // This means they see "Access Course in Moodle" directly on the course page.
        // Only auto-enroll if they have no enrollment record at all (preserves denied/cancelled states).
        if (!$enrollment && $user->isInternal()) {
            $enrollment = $this->autoEnrollInternalUser($user, $course);
        }

        // For external users, check if they have a CourseAccessRequest
        // (this is separate from the Enrollment system - external users go through
        // the request/approval workflow via CourseAccessRequest)
        $accessRequest = null;
        if (!$user->isInternal()) {
            $accessRequest = CourseAccessRequest::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->latest('requested_at')
                ->first();
        }

        // Determine what buttons/messages to show based on enrollment and access request status
        $accessLevel = $this->determineAccessLevel($user, $course, $enrollment, $accessRequest);

        // Log course view
        ActivityLogger::logCourse('viewed', $course,
            "User viewed course details: {$course->title}",
            [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'enrollment_status' => $enrollment?->status,
                'access_request_status' => $accessRequest?->status,
            ]
        );

        return view('courses.show', compact('course', 'enrollment', 'accessLevel'));
    }

    /**
     * Determine course access level for a user.
     *
     * ACCESS LEVEL FLOW:
     * ┌─────────────────────────────────────────────────────────────┐
     * │ Internal (MOH) users:                                       │
     * │   - Auto-enrolled in show() → always see "Access Course"    │
     * │   - Fallback: "Enroll Now" (auto-approved on click)         │
     * │                                                              │
     * │ External users:                                              │
     * │   - No request yet → "Request Access" (creates access req)  │
     * │   - Pending request → "Awaiting Approval"                   │
     * │   - Approved request → "Access Course in Moodle"            │
     * │   - Rejected request → "Request Again" option               │
     * └─────────────────────────────────────────────────────────────┘
     *
     * @param mixed $user The authenticated user
     * @param Course $course The course being viewed
     * @param Enrollment|null $enrollment User's enrollment record (if any)
     * @param CourseAccessRequest|null $accessRequest User's access request (external users only)
     * @return array Access level data for the view
     */
    private function determineAccessLevel($user, Course $course, ?Enrollment $enrollment, ?CourseAccessRequest $accessRequest = null): array
    {
        $isInternal = $user->isInternal();
        $hasMoodleSync = $course->moodle_course_id !== null;

        // ── STEP 1: Check existing enrollment status ──
        // This covers internal auto-enrolled users and any legacy enrollments
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

        // ── STEP 2: For external users, check CourseAccessRequest status ──
        // External users go through the request/approval workflow.
        // Their requests are managed by course admins in the Course Access admin page.
        if (!$isInternal && $accessRequest) {
            if ($accessRequest->isPending()) {
                return [
                    'level' => 'pending',
                    'can_access_moodle' => false,
                    'message' => 'Your access request is pending approval by a course administrator.',
                    'action' => 'wait'
                ];
            }

            if ($accessRequest->isApproved()) {
                // Approved access request - user can access the course
                return [
                    'level' => 'enrolled',
                    'can_access_moodle' => $hasMoodleSync && $user->moodle_user_id,
                    'message' => 'Your access has been approved! You can now access this course.',
                    'action' => $hasMoodleSync ? 'access_course' : 'view_content'
                ];
            }

            if ($accessRequest->isRejected()) {
                // Rejected - show reason and allow re-request
                $reason = 'Your access request was not approved.';
                if ($accessRequest->rejection_reason) {
                    $reason .= " Reason: {$accessRequest->rejection_reason}";
                }
                return [
                    'level' => 'denied',
                    'can_access_moodle' => false,
                    'message' => $reason,
                    'action' => 'contact',
                    'can_request_again' => true,
                ];
            }
        }

        // ── STEP 3: No enrollment or access request - show action button ──
        if ($isInternal) {
            // Safety fallback for internal users.
            // Normally, auto-enrollment in show() handles this, but if it failed
            // (e.g., DB error), we still show an "Enroll Now" button as backup.
            return [
                'level' => 'enroll',
                'can_access_moodle' => false,
                'message' => 'As Ministry of Health staff, you can enroll directly.',
                'action' => 'enroll_direct',
                'button_text' => 'Enroll Now',
                'button_style' => 'primary'
            ];
        } else {
            // External users: Show "Request Access" button.
            // This submits a CourseAccessRequest for admin review.
            return [
                'level' => 'request_access',
                'can_access_moodle' => false,
                'message' => 'This course requires approval. Submit a request to get access.',
                'action' => 'request_access',
                'button_text' => 'Request Access',
                'button_style' => 'secondary'
            ];
        }
    }

    /**
     * Auto-enroll an internal (MOH) user in a course.
     *
     * Internal users get immediate approved enrollment without clicking any button.
     * This creates the enrollment record and triggers Moodle sync in the background.
     *
     * @param mixed $user The authenticated internal user
     * @param Course $course The course to enroll in
     * @return Enrollment The created enrollment record
     */
    private function autoEnrollInternalUser($user, Course $course): Enrollment
    {
        // Create an approved enrollment immediately - MOH staff don't need approval
        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'approved',
        ]);

        // Sync to Moodle if the course has Moodle integration
        // This runs in the background so the page load isn't delayed
        if ($course->moodle_course_id) {
            try {
                // Ensure user has a Moodle account (creates one if needed)
                if (!$user->moodle_user_id) {
                    CreateOrLinkMoodleUser::dispatchSync($user);
                    $user->refresh();
                }

                // Enroll the user in the Moodle course SYNCHRONOUSLY.
                // This must complete before the page renders, otherwise the user
                // clicks "Access Course in Moodle" and lands as a guest because
                // the async queue job hasn't processed yet.
                if ($user->moodle_user_id) {
                    EnrollUserIntoMoodleCourse::dispatchSync($user, $course);
                }
            } catch (\Exception $e) {
                // Don't fail the page load if Moodle sync fails.
                // The enrollment is saved locally; Moodle sync can be retried later.
                Log::warning('Auto-enrollment Moodle sync failed', [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Log the auto-enrollment
        ActivityLogger::logEnrollment('auto_enrolled', $enrollment,
            "MOH staff auto-enrolled when viewing course: {$course->title}",
            [
                'user_email' => $user->email,
                'course_id' => $course->id,
                'moodle_synced' => (bool) $course->moodle_course_id,
            ]
        );

        return $enrollment;
    }

    /**
     * Direct access to Moodle course for enrolled users
     * MOH users with approved enrollment are redirected directly to Moodle via SSO
     */
    /**
     * Redirect user to Moodle course via SSO.
     *
     * This method ensures three things before redirecting:
     * 1. User has a Moodle account (creates one if missing)
     * 2. User is enrolled in the Moodle course (enrolls if missing)
     * 3. SSO login URL is generated (so user is logged in, not a guest)
     *
     * If ANY step fails, the user is redirected back with a clear error
     * instead of landing on Moodle as an unauthenticated guest.
     */
    public function accessMoodle($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        // -- STEP 1: Verify local enrollment exists --
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

        // -- STEP 2: Verify course has Moodle integration --
        if (!$course->moodle_course_id) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'This course is not yet available in Moodle.');
        }

        // -- STEP 3: Ensure user has a Moodle account with correct auth method --
        // Creates the Moodle account if missing, OR updates the existing account's
        // auth method to 'userkey' so SSO works. This is critical because users
        // who were found by email (not freshly created) may have auth='manual',
        // which causes auth_userkey_request_login_url to fail with HTTP 500.
        try {
            CreateOrLinkMoodleUser::dispatchSync($user);
            $user->refresh();
        } catch (\Exception $e) {
            Log::error('Failed to create/update Moodle account for user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('courses.show', $course)
                ->with('error', 'Could not set up your Moodle account: ' . $e->getMessage());
        }

        if (!$user->moodle_user_id) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Could not create your Moodle account. Please contact a system administrator.');
        }

        // -- STEP 4: Ensure user is enrolled in the Moodle course --
        // Calls enrol_manual_enrol_users on Moodle. Idempotent — safe to call
        // even if user is already enrolled.
        try {
            EnrollUserIntoMoodleCourse::dispatchSync($user, $course);
        } catch (\Exception $e) {
            Log::error('Failed to enroll user in Moodle course', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'moodle_course_id' => $course->moodle_course_id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('courses.show', $course)
                ->with('error', 'Could not enroll you in the Moodle course: ' . $e->getMessage());
        }

        // -- STEP 5: Generate SSO login URL and redirect --
        // Uses auth_userkey plugin to create a one-time login URL.
        // If SSO fails, redirect back with error instead of sending user as guest.
        try {
            $moodleService = app(\App\Services\MoodleService::class);
            $moodleUrl = $moodleService->generateCourseLoginUrl($user, $course->moodle_course_id);

            ActivityLogger::logMoodle('course_accessed',
                "User accessed Moodle course via SSO: {$course->title}",
                $course,
                [
                    'user_id' => $user->id,
                    'moodle_user_id' => $user->moodle_user_id,
                ]
            );

            return redirect()->away($moodleUrl);
        } catch (\Exception $e) {
            Log::error('Moodle SSO login failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'moodle_user_id' => $user->moodle_user_id,
                'course_id' => $course->id,
            ]);

            // Don't redirect to Moodle without SSO — user would land as guest.
            // Redirect back with a clear error explaining what needs to be configured.
            return redirect()->route('courses.show', $course)
                ->with('error',
                    'Could not log you into Moodle automatically. '
                    . 'Your account and enrollment have been created, but the SSO login plugin '
                    . '(auth_userkey) needs to be configured by a Moodle administrator. '
                    . 'Error: ' . $e->getMessage()
                );
        }
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
            // Access control
            'audience_type' => 'nullable|string|in:moh,external,all,MOH_ONLY,EXTERNAL_ONLY,BOTH',
            'is_free'       => 'nullable|boolean',
            // Optional Moodle fields
            'sync_to_moodle' => 'nullable|boolean',
            'moodle_course_shortname' => 'nullable|required_if:sync_to_moodle,true|string|max:255|unique:courses,moodle_course_shortname',
            'moodle_category_id' => 'nullable|required_if:sync_to_moodle,true|integer',
        ]);

        // Derive is_active from status so the learner dashboard can find this course
        $validated['is_active'] = ($validated['status'] === 'active');

        // Default audience_type if not provided
        $validated['audience_type'] = $validated['audience_type'] ?? 'moh';

        // Set creator
        $validated['creator_id'] = auth()->id();

        if ($request->hasFile('image')){
            $path = $request->file('image')->store('courses','public');
            $validated['image'] = $path;
        }

        // First, create the course locally in its own transaction
        DB::beginTransaction();
        try {
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create course locally', [
                'error' => $e->getMessage(),
                'course_title' => $validated['title']
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create course: ' . $e->getMessage());
        }

        // Then, sync to Moodle separately so the local course is preserved on failure
        if ($request->boolean('sync_to_moodle')) {
            try {
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

                return redirect()->route('courses.index')
                    ->with('success', 'Course created successfully and synced to Moodle.');

            } catch (MoodleException $e) {
                Log::error('Failed to sync course to Moodle', [
                    'error' => $e->getMessage(),
                    'course_id' => $course->id,
                    'course_title' => $course->title
                ]);

                // Log Moodle sync failure
                ActivityLogger::logMoodle('course_sync_failed',
                    "Failed to sync course to Moodle: {$course->title}",
                    $course,
                    [
                        'error' => $e->getMessage(),
                        'attempted_by' => auth()->user()->email
                    ],
                    'failed',
                    'error'
                );

                return redirect()->route('courses.index')
                    ->with('warning', 'Course created locally but failed to sync to Moodle. You can retry syncing from the course management page. Error: ' . $e->getMessage());
            }
        }

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
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
            // Access control
            'audience_type' => 'nullable|string|in:moh,external,all,MOH_ONLY,EXTERNAL_ONLY,BOTH',
            'is_free'       => 'nullable|boolean',
            'sync_to_moodle' => 'nullable|boolean',
            'moodle_course_shortname' => 'nullable|string|max:255|unique:courses,moodle_course_shortname,' . $id,
            'moodle_category_id' => 'nullable|integer',
        ]);

        // Derive is_active from status
        $validated['is_active'] = ($validated['status'] === 'active');

        // Store old values for logging
        $oldValues = $course->only(['title', 'description', 'status', 'audience_type', 'is_free', 'moodle_course_id']);

        if ($request->hasFile('image')){
            $path = $request->file('image')->store('courses','public');
            $validated['image'] = $path;
        }

        // First, save local changes in their own transaction
        DB::beginTransaction();
        try {
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update course locally', [
                'error' => $e->getMessage(),
                'course_id' => $course->id
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update course: ' . $e->getMessage());
        }

        // Then, handle Moodle sync separately so local changes are preserved on failure
        if ($request->boolean('sync_to_moodle')) {
            try {
                if ($course->moodle_course_id) {
                    $this->updateMoodleCourse($course);

                    ActivityLogger::logMoodle('course_updated',
                        "Course updated in Moodle: {$course->title}",
                        $course,
                        ['moodle_course_id' => $course->moodle_course_id]
                    );
                } else {
                    $moodleCourseId = $this->createMoodleCourse($course, $validated);
                    $course->update(['moodle_course_id' => $moodleCourseId]);

                    ActivityLogger::logMoodle('course_synced',
                        "Course newly synced to Moodle: {$course->title}",
                        $course,
                        ['moodle_course_id' => $moodleCourseId]
                    );
                }

                return redirect()->route('courses.index')
                    ->with('success', 'Course updated successfully and synced to Moodle.');

            } catch (MoodleException $e) {
                Log::error('Failed to sync course update to Moodle', [
                    'error' => $e->getMessage(),
                    'course_id' => $course->id
                ]);

                ActivityLogger::logMoodle('course_update_failed',
                    "Failed to update course in Moodle: {$course->title}",
                    $course,
                    ['error' => $e->getMessage()],
                    'failed',
                    'error'
                );

                return redirect()->route('courses.index')
                    ->with('warning', 'Course updated locally but Moodle sync failed. You can retry syncing from the course management page. Error: ' . $e->getMessage());
            }
        }

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully.');
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
        
        return view('courses.index', compact('courses', 'stats'));
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