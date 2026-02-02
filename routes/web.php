<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CourseController,
    EnrollmentController,
    ProfileController,
    UserManagementController,
    AdminController,
    NotificationController,
    DashboardController
};
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController,
    ResetPasswordController
};
use App\Http\Controllers\Admin\{
    RoleManagementController,
    MoodleCourseImportController,
    MoodleTestController,
    UserApprovalController,
    EnrollmentRequestController as AdminEnrollmentRequestController,
    AccountRequestController,
    CourseAccessRequestController
};
use App\Http\Controllers\{
    ExternalRegistrationController,
    CourseCatalogController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/*
|==========================================================================
| PUBLIC ROUTES (Landing Page)
|==========================================================================
*/
Route::get('/', function () {
    if (auth()->check()) {
        return view('landing.welcome', [
            'isAuthenticated' => true,
            'user' => auth()->user(),
            'enrolledCourses' => auth()->user()->hasRole(['admin', 'superadmin']) 
                ? \App\Models\Course::count() 
                : \App\Models\Enrollment::where('user_id', auth()->id())
                    ->where('status', 'approved')
                    ->count()
        ]);
    }
    return view('landing.welcome', ['isAuthenticated' => false]);
})->name('home');

Route::get('/home', fn() => redirect('/'));

/*
|==========================================================================
| SSO TEST ROUTE (DELETE AFTER TESTING!)
|==========================================================================
*/
Route::get('/test-sso', function () {
    $token = config('moodle.token');
    $domainname = config('moodle.base_url');
    $functionname = 'auth_userkey_request_login_url';

    // Get test user - include all fields
    $username = 'kyle.blackman';
    $email = 'kyle.blackman@health.gov.tt';
    $firstname = 'Kyle';
    $lastname = 'Blackman';
    $courseid = 2;

    // Try with all user fields
    $param = [
        'user' => [
            'username' => $username,
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname,
        ]
    ];

    $serverurl = $domainname . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=' . $functionname . '&moodlewsrestformat=json';

    $output = "<h2>Moodle SSO Test</h2>";
    $output .= "<p><strong>Username:</strong> " . htmlspecialchars($username) . "</p>";
    $output .= "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    $output .= "<p><strong>Moodle URL:</strong> " . htmlspecialchars($domainname) . "</p>";
    $output .= "<p><strong>Function:</strong> " . htmlspecialchars($functionname) . "</p>";
    $output .= "<p><strong>Params sent:</strong></p>";
    $output .= "<pre>" . htmlspecialchars(json_encode($param, JSON_PRETTY_PRINT)) . "</pre>";

    try {
        $response = \Illuminate\Support\Facades\Http::asForm()
            ->withoutVerifying()
            ->timeout(30)
            ->post($serverurl, $param);

        $rawBody = $response->body();
        $resp = $response->json();

        $output .= "<p><strong>HTTP Status:</strong> " . $response->status() . "</p>";
        $output .= "<p><strong>Raw Body:</strong></p>";
        $output .= "<pre>" . htmlspecialchars($rawBody) . "</pre>";
        $output .= "<p><strong>Parsed JSON:</strong></p>";
        $output .= "<pre>" . htmlspecialchars(json_encode($resp, JSON_PRETTY_PRINT)) . "</pre>";

        if (isset($resp['loginurl'])) {
            $loginurl = $resp['loginurl'];
            $finalUrl = $loginurl . '&wantsurl=' . urlencode("$domainname/course/view.php?id=$courseid");

            $output .= "<h3 style='color:green'>SUCCESS!</h3>";
            $output .= "<p><a href='" . htmlspecialchars($finalUrl) . "' style='padding:10px 20px; background:green; color:white; text-decoration:none; border-radius:5px;'>Click to Test SSO Login</a></p>";
        } else {
            $output .= "<h3 style='color:red'>FAILED - No loginurl in response</h3>";
            if (isset($resp['exception'])) {
                $output .= "<p><strong>Error:</strong> " . htmlspecialchars($resp['message'] ?? $resp['exception']) . "</p>";
            }
        }
    } catch (\Exception $e) {
        $output .= "<h3 style='color:red'>ERROR</h3>";
        $output .= "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    }

    $output .= "<hr><p style='color:red'><strong>⚠️ DELETE THIS ROUTE AFTER TESTING!</strong></p>";

    return $output;
})->name('test.sso');

/*
|==========================================================================
| GUEST ONLY ROUTES
|==========================================================================
*/
/*
|----------------------------------------------------------------------
| OTP VERIFICATION ROUTES (Accessible without full authentication)
|----------------------------------------------------------------------
| These routes are for the OTP verification flow. They must be accessible
| to users who have registered but not yet verified (session-based auth).
*/
Route::controller(LoginController::class)->prefix('auth/otp')->name('auth.otp.')->group(function () {
    Route::get('/verify', 'showOtpForm')->name('verify');
    Route::post('/verify', 'verifyOtp')->name('submit');
    Route::post('/resend', 'resendOtp')->name('resend');
});

Route::middleware('guest')->group(function () {
    // Authentication Routes (Rate limited to prevent brute force)
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')
            ->middleware('throttle:5,1') // 5 login attempts per minute
            ->name('login.submit');
    });

    // Registration Routes (Rate limited to prevent abuse)
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'showRegistrationForm')->name('register');
        Route::post('/register', 'register')
            ->middleware('throttle:5,1') // 5 registration attempts per minute
            ->name('register.submit');
    });

    // External User Registration Routes (Rate limited)
    // External users register via this page and can see courses and request access
    Route::controller(ExternalRegistrationController::class)->group(function () {
        Route::get('/register/external', 'create')->name('register.external');
        Route::post('/register/external', 'store')
            ->middleware('throttle:5,1') // 5 registration attempts per minute
            ->name('register.external.store');
    });

    // NOTE: Google OAuth has been REMOVED per requirements
    // All users now authenticate via username/password only
    // MOH Staff are identified by @health.gov.tt email domain

    // MOH Staff Account Request Routes
    // MOH staff submit request with password, but cannot login until approved
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/moh/request-account', 'showMohRequestForm')->name('moh.request-account');
        Route::post('/moh/request-account', 'submitMohRequest')
            ->middleware('throttle:5,1')
            ->name('moh.request-account.submit');
        Route::get('/moh/request-submitted', 'mohRequestSubmitted')->name('moh.request-submitted');
    });

    // Password Reset Routes
    Route::prefix('password')->name('password.')->group(function () {
        Route::controller(ForgotPasswordController::class)->group(function () {
            Route::get('/reset', 'showLinkRequestForm')->name('request');
            Route::post('/email', 'sendResetLinkEmail')->name('email');
        });

        Route::controller(ResetPasswordController::class)->group(function () {
            Route::get('/reset/{token}', 'showResetForm')->name('reset');
            Route::post('/reset', 'reset')->name('update');
        });
    });
});

/*
|==========================================================================
| AUTHENTICATED ROUTES (Login Required)
|==========================================================================
*/
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    /*
    |----------------------------------------------------------------------
    | Email Verification Routes (OTP-Based)
    |----------------------------------------------------------------------
    | These routes handle the OTP-based email verification for authenticated
    | users who haven't completed verification yet.
    */
    Route::prefix('email')->name('verification.')->group(function () {
        // Verification notice page - shows OTP entry or initiates OTP
        Route::get('/verify', [\App\Http\Controllers\Auth\VerificationController::class, 'notice'])
            ->name('notice');

        // Initiate OTP verification for logged-in users
        Route::post('/verify/initiate', [\App\Http\Controllers\Auth\VerificationController::class, 'initiateOtp'])
            ->middleware('throttle:6,1')
            ->name('initiate');

        // API endpoint to check verification status
        Route::get('/verification-check', fn() => response()->json([
            'verified' => auth()->user()->hasVerifiedEmail(),
            'otp_verified' => auth()->user()->initial_otp_completed ?? false,
            'status' => auth()->user()->verification_status ?? null,
            'can_resend' => auth()->user()->canRequestVerification() ?? false,
            'seconds_until_resend' => auth()->user()->seconds_until_can_request ?? 0,
        ]))->name('check');

        // Resend OTP code (replaces old email link resend)
        Route::post('/verification-notification', [\App\Http\Controllers\Auth\VerificationController::class, 'resendOtp'])
            ->middleware('throttle:6,1')
            ->name('send');

        // Legacy: Handle old email verification links gracefully
        // Redirect to OTP verification instead of using signed URL verification
        Route::get('/verify/{id}/{hash}', [\App\Http\Controllers\Auth\VerificationController::class, 'handleLegacyVerification'])
            ->middleware('signed')
            ->name('verify');
    });
    
    /*
    |----------------------------------------------------------------------
    | VERIFIED USER ROUTES
    |----------------------------------------------------------------------
    */
    Route::middleware('verified')->group(function () {

        // =========================================================================
        // DASHBOARD ROUTES (Role-aware redirects)
        // =========================================================================
        // Main dashboard entry - redirects based on role
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Admin Dashboard (SuperAdmin/Admin/Course Admin)
        Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
            ->middleware('role:admin|superadmin|course_admin')
            ->name('dashboard.admin');

        // SuperAdmin Dashboard (SuperAdmin ONLY)
        Route::get('/dashboard/superadmin', [DashboardController::class, 'superadmin'])
            ->middleware('role:superadmin')
            ->name('dashboard.superadmin');

        // Learner Dashboard (MOH Staff / External User)
        Route::get('/dashboard/learner', [DashboardController::class, 'learner'])->name('dashboard.learner');

        // Complete onboarding (dismiss welcome banner)
        Route::post('/dashboard/complete-onboarding', [DashboardController::class, 'completeOnboarding'])
            ->name('dashboard.complete-onboarding');

        // Account pending status page (for MOH Staff awaiting approval)
        Route::get('/account/pending', [DashboardController::class, 'accountPending'])->name('account.pending');
        
        // Course Routes - PROPERLY ORDERED (Static routes before dynamic)
        Route::prefix('courses')->name('courses.')->group(function () {
            // LIST route (static)
            Route::get('/', [CourseController::class, 'index'])->name('index');
            
            // CREATE route (static) - MUST come before {course} routes
            Route::middleware('role:admin|superadmin|course_admin')->group(function () {
                Route::get('/create', [CourseController::class, 'create'])->name('create');
                Route::post('/store', [CourseController::class, 'store'])->name('store');
            });
            
            // DYNAMIC routes - These come AFTER static routes
            Route::get('/{course}', [CourseController::class, 'show'])->name('show');
            Route::get('/{course}/register', [CourseController::class, 'register'])->name('register');
            Route::post('/{course}/enroll', [EnrollmentController::class, 'store'])->name('enroll.store');
            Route::get('/{course}/access-moodle', [CourseController::class, 'accessMoodle'])->name('access-moodle');
            
            // Admin dynamic routes
            Route::middleware('role:admin|superadmin|course_admin')->group(function () {
                Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('edit');
                Route::put('/{course}', [CourseController::class, 'update'])->name('update');
                Route::delete('/{course}', [CourseController::class, 'destroy'])->name('destroy');
                Route::post('/{course}/sync-to-moodle', [CourseController::class, 'syncToMoodle'])->name('syncToMoodle');
            });
        });
        
        // My Courses (Legacy)
        Route::get('/mycourses', [EnrollmentController::class, 'myCourses'])->name('mycourses');

        // My Learning (Enrolled courses with progress)
        Route::get('/my-learning', [EnrollmentController::class, 'myLearning'])->name('my-learning.index');

        // My Requests (Course access requests history)
        Route::get('/my-requests', [CourseAccessRequestController::class, 'userRequests'])->name('my-requests.index');

        // Course Catalog Routes (user-facing catalog)
        Route::prefix('catalog')->name('catalog.')->group(function () {
            Route::get('/', [CourseCatalogController::class, 'index'])->name('index');
            Route::get('/{course}', [CourseCatalogController::class, 'show'])->name('show');
        });

        // =========================================================================
        // COURSE ACCESS REQUEST ROUTES (For users to request course access)
        // Users can request access to courses with APPROVAL_REQUIRED enrollment
        // =========================================================================
        Route::post('/courses/{course}/request-access', [CourseAccessRequestController::class, 'store'])
            ->middleware('throttle:10,1') // Rate limit: 10 requests per minute
            ->name('courses.request-access');
        
        // Profile Management
        Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'show')->name('show');
            Route::get('/settings', 'settings')->name('settings');
            Route::post('/photo', 'updatePhoto')->name('photo');
            Route::post('/password', 'updatePassword')->name('password');
        });

        // =========================================================================
        // NOTIFICATION ROUTES
        // In-app notifications for users
        // =========================================================================
        Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/recent', 'recent')->name('recent');
            Route::post('/{notification}/read', 'markAsRead')->name('read');
            Route::post('/mark-all-read', 'markAllAsRead')->name('markAllRead');
            Route::delete('/{notification}', 'destroy')->name('destroy');
            Route::post('/clear-read', 'clearRead')->name('clearRead');
        });
        
        /*
        |----------------------------------------------------------------------
        | ADMIN, SUPERADMIN & COURSE ADMIN ROUTES
        |----------------------------------------------------------------------
        */
        Route::middleware('role:admin|superadmin|course_admin')->prefix('admin')->name('admin.')->group(function () {
            
            // Admin Dashboard
            Route::get('/dashboard', function() {
                $stats = [
                    'total_users' => \App\Models\User::count(),
                    'total_courses' => \App\Models\Course::count(),
                    'pending_enrollments' => \App\Models\Enrollment::where('status', 'pending')->count(),
                    'active_enrollments' => \App\Models\Enrollment::where('status', 'approved')->count(),
                ];
                
                return view()->exists('admin.dashboard') 
                    ? view('admin.dashboard', compact('stats'))
                    : redirect()->route('dashboard');
            })->name('dashboard');
            
            // Course Management (Admin View)
            Route::prefix('courses')->name('courses.')->controller(CourseController::class)->group(function () {
                Route::get('/', 'adminIndex')->name('index');
                Route::delete('/bulk-delete', 'bulkDelete')->name('bulkDelete');
                Route::post('/bulk-sync', 'bulkSync')->name('bulkSync');
                Route::post('/bulk-status', 'bulkUpdateStatus')->name('bulkStatus');
                Route::post('/{course}/toggle-status', 'toggleStatus')->name('toggleStatus');
            });
            
            // Enrollment Management
            Route::prefix('enrollments')->name('enrollments.')->group(function () {
                Route::get('/', [AdminController::class, 'enrollmentRequests'])->name('index');
                Route::post('/{id}/approve', [AdminController::class, 'approveEnrollment'])->name('approve');
                Route::post('/{id}/deny', [AdminController::class, 'denyEnrollment'])->name('deny');
                
                // Using EnrollmentController for these
                Route::controller(EnrollmentController::class)->group(function () {
                    Route::put('/{enrollment}', 'update')->name('update');
                    Route::delete('/{enrollment}', 'unenroll')->name('unenroll');
                    Route::post('/{enrollment}/sync-to-moodle', 'syncEnrollmentToMoodle')->name('syncToMoodle');
                });
            });
            
            // User Management
            Route::prefix('users')->name('users.')->controller(UserManagementController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                // IMPORTANT: Static routes MUST come before dynamic {user} routes
                Route::delete('/bulk-delete', 'bulkDelete')->name('bulkDelete');
                // Dynamic routes with {user} parameter
                // SECURITY: Role update is SuperAdmin-only to prevent privilege escalation
                Route::post('/{user}/role', 'updateRole')->middleware('role:superadmin')->name('updateRole');
                Route::delete('/{user}', 'destroy')->name('destroy');
                Route::patch('/{user}/suspend', 'suspend')->name('suspend');
                Route::patch('/{user}/reactivate', 'reactivate')->name('reactivate');
            });

            // External User Approval Management
            Route::prefix('users')->name('users.')->controller(UserApprovalController::class)->group(function () {
                Route::get('/pending', 'index')->name('pending');
                Route::post('/{user}/approve', 'approve')->name('approve');
                Route::post('/{user}/deny', 'deny')->name('deny');
                Route::post('/{user}/deactivate', 'deactivate')->name('deactivate');
                // Note: reactivate is already in UserManagementController above
            });

            // Enrollment Request Management (for admin to review enrollment requests)
            Route::prefix('enrollment-requests')->name('enrollment-requests.')->controller(AdminEnrollmentRequestController::class)->group(function () {
                Route::get('/', 'adminIndex')->name('index');
                Route::post('/{enrollmentRequest}/approve', 'approve')->name('approve');
                Route::post('/{enrollmentRequest}/deny', 'deny')->name('deny');
            });

            // =========================================================================
            // ACCOUNT REQUEST MANAGEMENT (Course Admin)
            // MOH Staff registration requests that need approval
            // =========================================================================
            Route::prefix('account-requests')->name('account-requests.')->middleware('course.admin')->controller(AccountRequestController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{accountRequest}', 'show')->name('show');
                Route::post('/{accountRequest}/approve', 'approve')->name('approve');
                Route::post('/{accountRequest}/reject', 'reject')->name('reject');
                Route::post('/bulk-approve', 'bulkApprove')->name('bulkApprove');
                Route::post('/bulk-approve-moh', 'bulkApproveAllMoh')->name('bulkApproveMoh');
            });

            // =========================================================================
            // COURSE ACCESS REQUEST MANAGEMENT (Course Admin)
            // Course enrollment requests that need approval
            // =========================================================================
            Route::prefix('course-access-requests')->name('course-access-requests.')->middleware('course.admin')->controller(CourseAccessRequestController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{courseAccessRequest}', 'show')->name('show');
                Route::post('/{courseAccessRequest}/approve', 'approve')->name('approve');
                Route::post('/{courseAccessRequest}/reject', 'reject')->name('reject');
                Route::post('/{courseAccessRequest}/revoke', 'revoke')->name('revoke');
                Route::post('/{courseAccessRequest}/retry-sync', 'retrySync')->name('retrySync');
                Route::post('/bulk-approve', 'bulkApprove')->name('bulkApprove');
            });
            
            // Role Management - SUPERADMIN ONLY (SECURITY: Prevents privilege escalation)
            Route::middleware('role:superadmin')->prefix('roles')->name('roles.')->controller(RoleManagementController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/assign/{user}', 'assignRole')->name('assign');
                Route::post('/bulk-assign', 'bulkAssignRoles')->name('bulkAssign');
            });
            // Activity Logs Management (Superadmin only)
            Route::middleware('role:superadmin')->prefix('activity-logs')->name('activity-logs.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('index');
                Route::get('/live', [\App\Http\Controllers\Admin\ActivityLogController::class, 'live'])->name('live');
                Route::get('/export', [\App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('export');
                Route::get('/{log}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('show');
            });
            
            // Moodle Integration
            Route::prefix('moodle')->name('moodle.')->group(function () {
                
                // Status & Connection
                Route::get('/status', function() {
                    $stats = [
                        'total_users' => \App\Models\User::count(),
                        'moodle_synced_users' => \App\Models\User::whereNotNull('moodle_user_id')->count(),
                        'total_courses' => \App\Models\Course::count(),
                        'moodle_synced_courses' => \App\Models\Course::whereNotNull('moodle_course_id')->count(),
                        'pending_enrollments' => \App\Models\Enrollment::where('status', 'pending')->count(),
                    ];
                    
                    return view()->exists('admin.moodle.status')
                        ? view('admin.moodle.status', compact('stats'))
                        : response()->json(['status' => 'success', 'data' => $stats]);
                })->name('status');
                
                Route::get('/test-connection', function() {
                    try {
                        $moodleService = app(\App\Services\MoodleService::class);
                        return $moodleService->testConnection()
                            ? response()->json(['status' => 'success', 'message' => 'Connection successful'])
                            : response()->json(['status' => 'error', 'message' => 'Connection failed']);
                    } catch (\Exception $e) {
                        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
                    }
                })->name('testConnection');
                
                Route::get('/failed-jobs', function() {
                    $failedJobs = \DB::table('failed_jobs')->latest()->take(20)->get();
                    return request()->wantsJson()
                        ? response()->json($failedJobs)
                        : (view()->exists('admin.moodle.failed-jobs')
                            ? view('admin.moodle.failed-jobs', compact('failedJobs'))
                            : response()->json($failedJobs));
                })->name('failedJobs');
                
                Route::post('/retry-failed', function(\Illuminate\Http\Request $request) {
                    $request->validate(['job_id' => 'required|exists:failed_jobs,id']);
                    try {
                        \Artisan::call('queue:retry', ['id' => $request->job_id]);
                        return back()->with('success', 'Job queued for retry');
                    } catch (\Exception $e) {
                        return back()->with('error', 'Failed to retry job: ' . $e->getMessage());
                    }
                })->name('retryFailed');
                
                // Moodle Test Routes (if controller exists)
                if (class_exists(MoodleTestController::class)) {
                    Route::controller(MoodleTestController::class)->prefix('test')->name('test.')->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/connection', 'testConnection')->name('connection');
                        Route::post('/create-user', 'createTestUser')->name('createUser');
                        Route::post('/sync-user/{userId}', 'syncUser')->name('syncUser');
                        Route::get('/logs', 'getLogs')->name('logs');
                    });
                }
                
                // User Sync
                Route::prefix('users')->name('users.')->group(function () {
                    Route::post('/{user}/sync', function(\App\Models\User $user) {
                        try {
                            \App\Jobs\CreateOrLinkMoodleUser::dispatch($user);
                            return back()->with('success', 'User sync queued');
                        } catch (\Exception $e) {
                            return back()->with('error', 'Failed to sync user: ' . $e->getMessage());
                        }
                    })->name('sync');
                    
                    Route::post('/bulk-sync', function(\Illuminate\Http\Request $request) {
                        $request->validate(['user_ids' => 'required|array']);
                        $count = 0;
                        foreach ($request->user_ids as $userId) {
                            $user = \App\Models\User::find($userId);
                            if ($user && !$user->moodle_user_id) {
                                \App\Jobs\CreateOrLinkMoodleUser::dispatch($user);
                                $count++;
                            }
                        }
                        return back()->with('success', "Queued {$count} users for Moodle sync");
                    })->name('bulkSync');
                });
                
                // Course Import/Sync
                Route::prefix('courses')->name('courses.')->group(function () {
                    if (class_exists(MoodleCourseImportController::class)) {
                        Route::controller(MoodleCourseImportController::class)->group(function () {
                            Route::get('/import', 'index')->name('import');
                            Route::post('/import/file', 'importFromFile')->name('import.file');
                            Route::post('/sync', 'syncFromMoodle')->name('sync');
                            Route::get('/export', 'exportFromMoodle')->name('export');
                        });
                    }
                    
                    Route::get('/template', function() {
                        $headers = [
                            'Content-Type' => 'text/csv',
                            'Content-Disposition' => 'attachment; filename="moodle_import_template.csv"',
                        ];
                        
                        $callback = function() {
                            $file = fopen('php://output', 'w');
                            fputcsv($file, ['moodle_id', 'shortname', 'fullname', 'summary', 'visible', 'categoryid']);
                            fputcsv($file, ['123', 'CS101', 'Introduction to Computer Science', 'Course description here', '1', '10']);
                            fclose($file);
                        };
                        
                        return response()->stream($callback, 200, $headers);
                    })->name('template');
                    
                    Route::get('/missing', fn() => view()->exists('admin.moodle.missing-courses')
                        ? view('admin.moodle.missing-courses')
                        : response()->json(['message' => 'View not found'])
                    )->name('missing');
                    
                    Route::post('/{course}/sync-enrollments', [EnrollmentController::class, 'bulkSyncCourseEnrollments'])->name('sync.enrollments');
                });
            });
        });
        
        // Development/Testing Routes
        if (app()->environment('local', 'development')) {
            Route::get('/test-enrollment-flow', function () {
                return view('dev.test-enrollment', [
                    'users' => \App\Models\User::latest()->take(10)->get(),
                    'courses' => \App\Models\Course::all(),
                    'totalUsers' => \App\Models\User::count(),
                    'moodleUsers' => \App\Models\User::whereNotNull('moodle_user_id')->count(),
                    'pendingUsers' => \App\Models\User::count() - \App\Models\User::whereNotNull('moodle_user_id')->count(),
                ]);
            })->name('test.enrollment.flow');
        }
    });
});

/*
|==========================================================================
| SUPERADMIN ONLY ROUTES
|==========================================================================
*/
Route::middleware(['auth', 'verified', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('index');
    Route::get('/live', [\App\Http\Controllers\Admin\ActivityLogController::class, 'live'])->name('live');
    Route::get('/export', [\App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('export');
    Route::get('/{log}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('show');
});

/*
|==========================================================================
| FALLBACK ROUTE (Must be last!)
|==========================================================================
*/
Route::fallback(function () {
    if (view()->exists('errors.404')) {
        return response()->view('errors.404', [], 404);
    }
    
    return response('
        <!DOCTYPE html>
        <html>
        <head>
            <title>Page Not Found</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                h1 { color: #666; }
                a { color: #007bff; text-decoration: none; }
            </style>
        </head>
        <body>
            <h1>404 - Page Not Found</h1>
            <p>The page you are looking for could not be found.</p>
            <a href="/">Go to Homepage</a>
        </body>
        </html>
    ', 404);
});