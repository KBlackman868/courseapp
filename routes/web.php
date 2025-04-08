<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('landing.welcome');
})->name('welcome');

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

    /*
    |----------------------------------------------------------------------
    | Admin-only routes: List users, update roles, manage enrollments.
    |----------------------------------------------------------------------
    */
    Route::middleware([\Spatie\Permission\Middleware\RoleMiddleware::class . ':admin|superadmin'])->group(function () {
        // Route to list all users
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');

        // Route to update a user's role
        Route::post('/admin/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('admin.users.updateRole');

        // Route to list enrollments (e.g., for approval)
        Route::get('/admin/enrollments', [EnrollmentController::class, 'index'])->name('admin.enrollments.index');

        // Route to update enrollment status (PUT request)
        Route::put('/admin/enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('admin.enrollments.update');

        // New route: Admin unenrolls a user (DELETE request)
        Route::delete('/admin/enrollments/{enrollment}', [EnrollmentController::class, 'unenroll'])->name('admin.enrollments.unenroll');
    });
});
