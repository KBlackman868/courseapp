<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', fn() => view('landing.welcome'))->name('welcome');

// Password reset (forgot / email link)
Route::middleware('guest')->group(function(){
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
         ->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
         ->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
         ->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
         ->name('password.update');

    // Auth
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});

// Logout must remain outside guest
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

    // Home / Courses
    Route::get('/home', [CourseController::class, 'index'])->name('home');
    Route::resource('courses', CourseController::class);
    Route::get('/courses/{course}/register', [CourseController::class, 'register'])
         ->name('courses.register');
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])
         ->name('courses.enroll.store');
    Route::get('/mycourses', [EnrollmentController::class, 'myCourses'])
         ->name('mycourses');

    // Profile & Settings
    Route::get('/profile',          [ProfileController::class, 'show'])            ->name('profile.show');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])        ->name('profile.settings');
    Route::post('/profile/photo',   [ProfileController::class, 'updatePhoto'])     ->name('profile.photo');
    // Change password form
    Route::get('/password/change',  [ProfileController::class, 'showChangeForm'])  ->name('password.change');
    Route::post('/profile/password',[ProfileController::class, 'updatePassword'])  ->name('profile.password');

    /*
    |--------------------------------------------------------------------------
    | Admin-only routes: List users, update roles, manage enrollments.
    |--------------------------------------------------------------------------
    */
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::class . ':admin|superadmin')
        ->group(function () {
            Route::get('/admin/users',       [UserManagementController::class, 'index'])
                 ->name('admin.users.index');
            Route::post('/admin/users/{user}/role',
                 [UserManagementController::class, 'updateRole'])
                 ->name('admin.users.updateRole');

            Route::get('/admin/enrollments',        [EnrollmentController::class, 'index'])
                 ->name('admin.enrollments.index');
            Route::put('/admin/enrollments/{enrollment}',
                 [EnrollmentController::class, 'update'])
                 ->name('admin.enrollments.update');
            Route::delete('/admin/enrollments/{enrollment}',
                 [EnrollmentController::class, 'unenroll'])
                 ->name('admin.enrollments.unenroll');
        });
});
