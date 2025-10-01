<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CourseController,
    EnrollmentController,
    ProfileController,
    UserManagementController
};
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController,
    ResetPasswordController
};
use App\Http\Controllers\Admin\{
    RoleManagementController,
    MoodleCourseImportController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/*
|==========================================================================
| PUBLIC ROUTES (No Authentication Required)
|==========================================================================
*/
Route::get('/', fn() => view('landing.welcome'))->name('welcome');

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
    
    // Password Reset Routes
    Route::prefix('password')->name('password.')->group(function () {
        Route::get('/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('request');
        Route::post('/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('email');
        Route::get('/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset');
        Route::post('/reset', [ResetPasswordController::class, 'reset'])->name('update');
    });
});

/*
|==========================================================================
| AUTHENTICATED ROUTES (Login Required)
|==========================================================================
*/
Route::middleware('auth')->group(function () {
    
    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    /*
    |----------------------------------------------------------------------
    | Email Verification Routes (Auth but not verified)
    |----------------------------------------------------------------------
    */
    Route::prefix('email')->name('verification.')->group(function () {
        Route::get('/verify', fn() => view('auth.verify-email'))->name('notice');
        
        Route::get('/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
            $request->fulfill();
            return redirect('/home')->with('success', 'Email verified successfully!');
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
        
        // Dashboard & Home
        Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');
        Route::get('/home', [CourseController::class, 'index'])->name('home');
        
        // Course Management
        Route::resource('courses', CourseController::class);
        Route::prefix('courses/{course}')->name('courses.')->group(function () {
            Route::get('/register', [CourseController::class, 'register'])->name('register');
            Route::post('/enroll', [EnrollmentController::class, 'store'])->name('enroll.store');
        });
        
        Route::get('/mycourses', [EnrollmentController::class, 'myCourses'])->name('mycourses');
        
        // Profile Management
        Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'show')->name('show');
            Route::get('/settings', 'settings')->name('settings');
            Route::post('/photo', 'updatePhoto')->name('photo');
            Route::post('/password', 'updatePassword')->name('password');
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
        
        /*
        |----------------------------------------------------------------------
        | ADMIN & SUPERADMIN ROUTES
        |----------------------------------------------------------------------
        */
        Route::middleware('role:admin|superadmin')->prefix('admin')->name('admin.')->group(function () {
            
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
            
            // Course Management
            Route::prefix('courses')->name('courses.')->group(function () {
                Route::get('/', [CourseController::class, 'adminIndex'])->name('index');
                Route::delete('/bulk-delete', [CourseController::class, 'bulkDelete'])->name('bulkDelete');
                Route::post('/bulk-status', [CourseController::class, 'bulkUpdateStatus'])->name('bulkStatus');
            });
            // Enrollment Management
            Route::prefix('enrollments')->name('enrollments.')->controller(EnrollmentController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::put('/{enrollment}', 'update')->name('update');
                Route::delete('/{enrollment}', 'unenroll')->name('unenroll');
                Route::post('/{enrollment}/sync-to-moodle', 'syncEnrollmentToMoodle')->name('syncToMoodle');
            });
            
            // Moodle Integration
            Route::prefix('moodle')->name('moodle.')->group(function () {
                
                // Dashboard & Status
                Route::get('/status', 'App\Http\Controllers\Admin\MoodleIntegrationController@status')->name('status');
                Route::get('/test-connection', 'App\Http\Controllers\Admin\MoodleIntegrationController@testConnection')->name('testConnection');
                Route::get('/failed-jobs', 'App\Http\Controllers\Admin\MoodleIntegrationController@failedJobs')->name('failedJobs');
                Route::post('/retry-failed', 'App\Http\Controllers\Admin\MoodleIntegrationController@retryFailed')->name('retryFailed');
                
                // User Sync
                Route::prefix('users')->name('users.')->group(function () {
                    Route::post('/{user}/sync', 'App\Http\Controllers\Admin\MoodleIntegrationController@syncUser')->name('sync');
                    Route::post('/bulk-sync', 'App\Http\Controllers\Admin\MoodleIntegrationController@bulkSyncUsers')->name('bulkSync');
                });
                
                // Course Import/Sync
                Route::prefix('courses')->name('courses.')->controller(MoodleCourseImportController::class)->group(function () {
                    Route::get('/import', 'index')->name('import');
                    Route::post('/import/file', 'importFromFile')->name('import.file');
                    Route::post('/sync', 'syncFromMoodle')->name('sync');
                    Route::get('/export', 'exportFromMoodle')->name('export');
                    Route::get('/template', 'downloadTemplate')->name('template');
                    Route::get('/status', 'courseStatus')->name('status');
                    Route::get('/missing', 'missingCourses')->name('missing');
                    Route::post('/{course}/sync', 'syncSingleCourse')->name('single.sync');
                    Route::post('/{course}/sync-enrollments', [EnrollmentController::class, 'bulkSyncCourseEnrollments'])->name('sync.enrollments');
                });
            });
        });
        
        // Course-Moodle Sync (Admin only)
        Route::middleware('role:admin|superadmin')->group(function () {
            Route::post('/courses/{course}/sync-to-moodle', [CourseController::class, 'syncToMoodle'])->name('courses.syncToMoodle');
        });
    });
});

/*
|==========================================================================
| SUPERADMIN ONLY ROUTES
|==========================================================================
*/
Route::middleware(['auth', 'verified', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // Add superadmin-only routes here
    // Examples: System settings, database maintenance, etc.
});

/*
|==========================================================================
| FALLBACK ROUTE (Must be last!)
|==========================================================================
*/
Route::fallback(fn() => view('errors.404'));