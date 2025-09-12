<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\http\Controller\Auth\ProfileController;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('landing.welcome');
})->name('welcome');


// Show the form to request a password reset link
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->middleware('guest')
    ->name('password.request');

// Handle the form submission: send a password reset link to the given email.
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');

// Show the form to reset the password (with token)
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

// Handle the password reset form submission
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    // Home page - list all courses via the CourseController index method
    Route::get('/home', [CourseController::class, 'index'])->name('home');

    /*
    |----------------------------------------------------------------------
    | Course Routes (Resource)
    |----------------------------------------------------------------------
    */
    Route::resource('courses', CourseController::class);

    // GET route to display the registration page for a course
    Route::get('/courses/{course}/register', [CourseController::class, 'register'])->name('courses.register');

    // POST route to handle enrollment
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])->name('courses.enroll.store');

    // Route to show my courses listing for the authenticated user
    Route::get('/mycourses', [EnrollmentController::class, 'myCourses'])->name('mycourses');
        // Profile “view”
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        // Settings page
        Route::get('/profile/settings', [\App\Http\Controllers\ProfileController::class, 'settings'])->name('profile.settings');
        // Upload new avatar
        Route::post('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo');
        // Change password
        Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    /*
    |----------------------------------------------------------------------
    | Admin-only routes: List users, update roles, manage enrollments.
    |----------------------------------------------------------------------
    */
    
    Route::middleware([\Spatie\Permission\Middleware\RoleMiddleware::class . ':admin|superadmin'])->group(function () {
        // Route to list all users
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        // Add this route for admins
        Route::post('/courses/{course}/sync-to-moodle', [CourseController::class, 'syncToMoodle'])
        ->name('courses.syncToMoodle')
        ->middleware(['auth', 'role:admin|superadmin']);
         // Route to update a user's role
        Route::post('/admin/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('admin.users.updateRole');

        // Route to list enrollments (e.g., for approval)
        Route::get('/admin/enrollments', [EnrollmentController::class, 'index'])->name('admin.enrollments.index');

        // Route to update enrollment status (PUT request)
        Route::put('/admin/enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('admin.enrollments.update');

        // New route: Admin unenrolls a user (DELETE request)
        Route::delete('/admin/enrollments/{enrollment}', [EnrollmentController::class, 'unenroll'])->name('admin.enrollments.unenroll');
        // Profile “view”
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        // Settings page
        Route::get('/profile/settings', [\App\Http\Controllers\ProfileController::class, 'settings'])->name('profile.settings');
        // Upload new avatar
        Route::post('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo');
        // Change password
        Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    });
});
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// User info endpoint (optional, for testing Sanctum auth)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Moodle Provisioning API v1
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    
    Route::prefix('moodle')->group(function () {
        // User provisioning
        Route::post('users', [MoodleUserController::class, 'store'])
            ->name('api.v1.moodle.users.store');
        
        Route::put('users/{user}', [MoodleUserController::class, 'update'])
            ->name('api.v1.moodle.users.update');
        
        // Course enrollment
        Route::post('enrolments', [MoodleEnrolmentController::class, 'store'])
            ->name('api.v1.moodle.enrolments.store');
        
        // Optional: Bulk operations
        Route::post('users/bulk', [MoodleUserController::class, 'bulkCreate'])
            ->name('api.v1.moodle.users.bulk');
        
        Route::post('enrolments/bulk', [MoodleEnrolmentController::class, 'bulkStore'])
            ->name('api.v1.moodle.enrolments.bulk');
    });
});
