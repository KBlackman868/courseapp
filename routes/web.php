<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Models\Course;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

# Landing Page
Route::get('/', function () {
    return view('landing.welcome');
});

Route::middleware(['auth'])->group(function() {
    // Dashboard route, protected by the auth middleware
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    });
    //->middleware('auth')->name('dashboard');
    Route::get('/course', function () {
        return view('courses.course');
    });
    Route::get('/course/register/{id}', function ($id) {
        $course = Course::find($id);
        return view('courses.register', ['course'=> $course]);
    })->name('courses.register');

    //Home page
    Route::get('/home', function () {
        return view('courses.home');
    });
    //Show enrollment page
    Route::get('/course/{course}', [CourseController::class, 'show'])->name('course.show');

    //Enrollment Page
    Route::post('/enroll/{course}', [EnrollmentController::class, 'store'])->name('enroll.store');

});

// Display registration form
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Handle registration submission
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

//Display Login Form
Route::get('/login',[LoginController::class, 'showLoginForm'])->name('login');

//Login User Into System
Route::post('/login',[LoginController::class, 'login'])->name('login.submit');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
