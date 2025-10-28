<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CourseController,
    EnrollmentController,
    ProfileController,
    UserManagementController,
    AdminController
};
use App\Http\Controllers\Auth\{
    GoogleAuthController, 
    LoginController,
    RegisterController,
    ForgotPasswordController,
    ResetPasswordController
};
use App\Http\Controllers\Admin\{
    RoleManagementController,
    MoodleCourseImportController,
    MoodleTestController
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
| GUEST ONLY ROUTES
|==========================================================================
*/
Route::middleware('guest')->group(function () {
    // Authentication Routes
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->name('login.submit');
    });
    
    // Registration Routes
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'showRegistrationForm')->name('register');
        Route::post('/register', 'register')->name('register.submit');
    });
    
        // Google OAuth Routes
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])
        ->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');

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
    | Email Verification Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('email')->name('verification.')->group(function () {
        Route::get('/verify', fn() => view('auth.verify-email'))->name('notice');
        
        Route::get('/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
            $request->fulfill();
            return redirect()->route('dashboard')->with('success', 'Email verified successfully!');
        })->middleware('signed')->name('verify');
        
        Route::get('/verification-check', fn() => response()->json([
            'verified' => auth()->user()->hasVerifiedEmail(),
            'status' => auth()->user()->verification_status ?? null,
            'can_resend' => auth()->user()->canRequestVerification() ?? false,
            'seconds_until_resend' => auth()->user()->seconds_until_can_request ?? 0,
        ]))->name('check');
        
        Route::post('/verification-notification', function (\Illuminate\Http\Request $request) {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('message', 'Verification link sent!');
        })->middleware('throttle:6,1')->name('send');
    });
    
    /*
    |----------------------------------------------------------------------
    | VERIFIED USER ROUTES
    |----------------------------------------------------------------------
    */
    Route::middleware('verified')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');
        
        // Course Routes - PROPERLY ORDERED (Static routes before dynamic)
        Route::prefix('courses')->name('courses.')->group(function () {
            // LIST route (static)
            Route::get('/', [CourseController::class, 'index'])->name('index');
            
            // CREATE route (static) - MUST come before {course} routes
            Route::middleware('role:admin|superadmin')->group(function () {
                Route::get('/create', [CourseController::class, 'create'])->name('create');
                Route::post('/store', [CourseController::class, 'store'])->name('store');
            });
            
            // DYNAMIC routes - These come AFTER static routes
            Route::get('/{course}', [CourseController::class, 'show'])->name('show');
            Route::get('/{course}/register', [CourseController::class, 'register'])->name('register');
            Route::post('/{course}/enroll', [EnrollmentController::class, 'store'])->name('enroll.store');
            
            // Admin dynamic routes
            Route::middleware('role:admin|superadmin')->group(function () {
                Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('edit');
                Route::put('/{course}', [CourseController::class, 'update'])->name('update');
                Route::delete('/{course}', [CourseController::class, 'destroy'])->name('destroy');
                Route::post('/{course}/sync-to-moodle', [CourseController::class, 'syncToMoodle'])->name('syncToMoodle');
            });
        });
        
        // My Courses
        Route::get('/mycourses', [EnrollmentController::class, 'myCourses'])->name('mycourses');
        
        // Profile Management
        Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'show')->name('show');
            Route::get('/settings', 'settings')->name('settings');
            Route::post('/photo', 'updatePhoto')->name('photo');
            Route::post('/password', 'updatePassword')->name('password');
        });
        
        /*
        |----------------------------------------------------------------------
        | ADMIN & SUPERADMIN ROUTES
        |----------------------------------------------------------------------
        */
        Route::middleware('role:admin|superadmin')->prefix('admin')->name('admin.')->group(function () {
            
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
                Route::post('/{user}/role', 'updateRole')->name('updateRole');
                Route::delete('/{user}', 'destroy')->name('destroy');
                Route::patch('/{user}/suspend', 'suspend')->name('suspend');
                Route::patch('/{user}/reactivate', 'reactivate')->name('reactivate');
                Route::delete('/bulk-delete', 'bulkDelete')->name('bulkDelete');
            });
            
            // Role Management
            Route::prefix('roles')->name('roles.')->controller(RoleManagementController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/assign/{user}', 'assignRole')->name('assign');
                Route::post('/bulk-assign', 'bulkAssignRoles')->name('bulkAssign');
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
    // Add superadmin-only routes here if needed
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