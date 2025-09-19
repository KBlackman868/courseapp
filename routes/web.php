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
// Note: Removed duplicate Auth\ProfileController import

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Landing page - accessible to everyone
Route::get('/', function () {
    return view('landing.welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest Only)
|--------------------------------------------------------------------------
| These routes are only accessible to non-authenticated users
*/

Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    
    // Registration routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
    
    // Password reset routes
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Logout route (requires authentication but placed here for organization)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Requires Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    /*
    |----------------------------------------------------------------------
    | Dashboard & Home
    |----------------------------------------------------------------------
    */
    
    // Main dashboard
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');
    
    // Home page - lists all available courses
    Route::get('/home', [CourseController::class, 'index'])->name('home');
    
    /*
    |----------------------------------------------------------------------
    | Course Management (CRUD + Enrollment)
    |----------------------------------------------------------------------
    */
    
    // Resource routes for courses (index, create, store, show, edit, update, destroy)
    Route::resource('courses', CourseController::class);
    
    // Course enrollment routes
    Route::get('/courses/{course}/register', [CourseController::class, 'register'])
        ->name('courses.register'); // Shows enrollment page for specific course
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])
        ->name('courses.enroll.store'); // Handles enrollment submission
    
    // User's enrolled courses
    Route::get('/mycourses', [EnrollmentController::class, 'myCourses'])->name('mycourses');
    
    /*
    |----------------------------------------------------------------------
    | User Profile Management
    |----------------------------------------------------------------------
    */
    
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo');
        Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });
    
    /*
    |----------------------------------------------------------------------
    | Testing & Development Routes (Protected)
    |----------------------------------------------------------------------
    */
    
    // Moodle integration test page - shows enrollment flow status
    Route::get('/test-enrollment-flow', function () {
        $users = \App\Models\User::latest()->take(10)->get();
        $courses = \App\Models\Course::all();
        
        // Calculate statistics
        $totalUsers = \App\Models\User::count();
        $moodleUsers = \App\Models\User::whereNotNull('moodle_user_id')->count();
        $pendingUsers = $totalUsers - $moodleUsers;
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Enrollment-Based Moodle Sync Test</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 p-8">
            <div class="max-w-6xl mx-auto">
                <h1 class="text-3xl font-bold mb-8">🎓 Enrollment-Based Moodle User Creation</h1>
                
                <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-6">
                    <h2 class="font-bold text-blue-800 mb-2">How it works now:</h2>
                    <ol class="list-decimal list-inside text-blue-700">
                        <li>User registers → Created ONLY in Laravel (no Moodle account)</li>
                        <li>User enrolls in first course → Moodle account created automatically</li>
                        <li>User enrolls in more courses → Uses existing Moodle account</li>
                    </ol>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <!-- Recent Users -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold mb-4">Recent Users</h2>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left pb-2">User</th>
                                    <th class="text-left pb-2">Moodle Status</th>
                                </tr>
                            </thead>
                            <tbody>';
        
        foreach ($users as $user) {
            $status = $user->moodle_user_id 
                ? '✅ Has Moodle (ID: ' . $user->moodle_user_id . ')' 
                : '⏳ No Moodle (will create on enrollment)';
            $rowClass = $user->moodle_user_id ? 'bg-green-50' : 'bg-yellow-50';
            
            $html .= "<tr class='border-b $rowClass'>
                        <td class='py-2'>{$user->email}</td>
                        <td class='py-2'>$status</td>
                      </tr>";
        }
        
        $html .= '</tbody>
                        </table>
                    </div>
                    
                    <!-- Courses -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold mb-4">Available Courses</h2>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left pb-2">Course</th>
                                    <th class="text-left pb-2">Moodle ID</th>
                                </tr>
                            </thead>
                            <tbody>';
        
        foreach ($courses as $course) {
            $moodleStatus = $course->moodle_course_id 
                ? '✅ ' . $course->moodle_course_id 
                : '❌ Not synced';
            
            $html .= "<tr class='border-b'>
                        <td class='py-2'>{$course->title}</td>
                        <td class='py-2'>$moodleStatus</td>
                      </tr>";
        }
        
        $html .= '</tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Test Scenario -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-xl font-bold mb-4">📝 Test the Flow</h2>
                    <ol class="list-decimal list-inside space-y-2">
                        <li><strong>Register a new user</strong> - They should NOT get a Moodle account</li>
                        <li><strong>Login as that user</strong></li>
                        <li><strong>Enroll in a course</strong> - NOW they should get a Moodle account</li>
                        <li><strong>Check this page again</strong> - User should now have a Moodle ID</li>
                    </ol>
                </div>
                
                <!-- Current Stats -->
                <div class="bg-gray-100 rounded-lg p-6 mt-6">
                    <h2 class="text-xl font-bold mb-4">📊 Current Statistics</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-white p-4 rounded">
                            <div class="text-2xl font-bold">' . $totalUsers . '</div>
                            <div class="text-gray-600">Total Users</div>
                        </div>
                        <div class="bg-white p-4 rounded">
                            <div class="text-2xl font-bold text-green-600">' . $moodleUsers . '</div>
                            <div class="text-gray-600">Have Moodle Accounts</div>
                        </div>
                        <div class="bg-white p-4 rounded">
                            <div class="text-2xl font-bold text-yellow-600">' . $pendingUsers . '</div>
                            <div class="text-gray-600">No Moodle Yet</div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    })->name('test.enrollment.flow');
});

/*
|--------------------------------------------------------------------------
| Admin & Super Admin Routes
|--------------------------------------------------------------------------
| Routes accessible only to users with admin or superadmin roles
*/

Route::middleware(['auth', 'role:admin|superadmin'])->group(function () {
    
    /*
    |----------------------------------------------------------------------
    | User Management
    |----------------------------------------------------------------------
    */
    
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        // List all users with filtering and pagination
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        
        // Update a specific user's role
        Route::post('/{user}/role', [UserManagementController::class, 'updateRole'])->name('updateRole');
        
        // User suspension and deletion
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/suspend', [UserManagementController::class, 'suspend'])->name('suspend');
        Route::patch('/{user}/reactivate', [UserManagementController::class, 'reactivate'])->name('reactivate');

        //Role Management
        Route::prefix('admin/roles')->name('admin.roles.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\RoleManagementController::class, 'index'])->name('index');
            Route::post('/assign/{user}', [\App\Http\Controllers\Admin\RoleManagementController::class, 'assignRole'])->name('assign');
            Route::post('/bulk-assign', [\App\Http\Controllers\Admin\RoleManagementController::class, 'bulkAssignRoles'])->name('bulkAssign');
        });
        
    // User role assignment
    Route::post('users/{user}/assign-role', [RolePermissionController::class, 'assignRole'])
        ->name('users.assign-role');
        // Bulk operations
        Route::delete('/bulk-delete', [UserManagementController::class, 'bulkDelete'])->name('bulkDelete');
    });
    
    /*
    |----------------------------------------------------------------------
    | Enrollment Management (Admin Review & Approval)
    |----------------------------------------------------------------------
    */
    
    Route::prefix('admin/enrollments')->name('admin.enrollments.')->group(function () {
        // View all enrollments (pending, approved, denied)
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        
        // Update enrollment status (approve/deny)
        Route::put('/{enrollment}', [EnrollmentController::class, 'update'])->name('update');
        
        // Admin force unenroll a user from a course
        Route::delete('/{enrollment}', [EnrollmentController::class, 'unenroll'])->name('unenroll');
        
        // Manual Moodle sync for specific enrollment
        Route::post('/{enrollment}/sync-to-moodle', function(\App\Models\Enrollment $enrollment) {
            if ($enrollment->status !== 'approved') {
                return back()->with('error', 'Only approved enrollments can be synced');
            }
            
            if (!$enrollment->course->moodle_course_id) {
                return back()->with('error', 'Course does not have a Moodle course ID');
            }
            
            // Create/link user first if needed
            if (!$enrollment->user->moodle_user_id) {
                \App\Jobs\CreateOrLinkMoodleUser::dispatchSync($enrollment->user);
            }
            
            // Then enroll in Moodle course
            \App\Jobs\EnrollUserIntoMoodleCourse::dispatch(
                $enrollment->user,
                $enrollment->course->moodle_course_id
            );
            
            return back()->with('success', 'Enrollment sync to Moodle initiated');
        })->name('syncToMoodle');
    });
    
    /*
    |----------------------------------------------------------------------
    | Course Moodle Integration
    |----------------------------------------------------------------------
    */
    
    // Sync a course to Moodle (creates course in Moodle if not exists)
    Route::post('/courses/{course}/sync-to-moodle', [CourseController::class, 'syncToMoodle'])
        ->name('courses.syncToMoodle');
    
    /*
    |----------------------------------------------------------------------
    | Moodle Integration Admin Dashboard
    |----------------------------------------------------------------------
    */
    
    Route::prefix('admin/moodle')->name('admin.moodle.')->group(function () {
        
        // Moodle integration status dashboard
        Route::get('/status', function() {
            $stats = [
                'users_total' => \App\Models\User::count(),
                'users_with_moodle' => \App\Models\User::whereNotNull('moodle_user_id')->count(),
                'courses_total' => \App\Models\Course::count(),
                'courses_with_moodle' => \App\Models\Course::whereNotNull('moodle_course_id')->count(),
                'pending_sync' => \App\Models\Enrollment::where('status', 'approved')
                    ->whereHas('user', fn($q) => $q->whereNull('moodle_user_id'))
                    ->count(),
                'failed_jobs' => \DB::table('failed_jobs')->count(),
            ];
            
            // If you don't have the view yet, return JSON for now
            return response()->json($stats);
            // return view('admin.moodle.status', compact('stats'));
        })->name('status');
        
        // Test Moodle API connection
        Route::get('/test-connection', function() {
            try {
                $client = new \App\Services\MoodleClient();
                $result = $client->call('core_webservice_get_site_info');
                
                return response()->json([
                    'status' => 'success',
                    'site' => $result['sitename'] ?? 'Unknown',
                    'version' => $result['release'] ?? 'Unknown',
                    'username' => $result['username'] ?? 'Unknown',
                    'userid' => $result['userid'] ?? 'Unknown',
                    'functions_available' => count($result['functions'] ?? [])
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'moodle_url' => config('services.moodle.base_url'),
                    'token_configured' => !empty(config('services.moodle.token'))
                ], 500);
            }
        })->name('testConnection');
        
        // Sync specific user to Moodle
        Route::post('/users/{user}/sync', function(\App\Models\User $user) {
            \App\Jobs\CreateOrLinkMoodleUser::dispatch($user);
            return back()->with('success', 'User sync initiated for ' . $user->email);
        })->name('users.sync');
        
        // Bulk sync all users without Moodle accounts
        Route::post('/users/bulk-sync', function() {
            $users = \App\Models\User::whereNull('moodle_user_id')->get();
            $count = 0;
            
            foreach ($users as $user) {
                \App\Jobs\CreateOrLinkMoodleUser::dispatch($user);
                $count++;
            }
            
            return response()->json([
                'status' => 'success',
                'message' => "Initiated sync for $count users"
            ]);
        })->name('users.bulkSync');
        
        // View and manage failed jobs
        Route::get('/failed-jobs', function() {
            $failedJobs = \DB::table('failed_jobs')
                ->orderBy('failed_at', 'desc')
                ->paginate(20);
            
            return response()->json($failedJobs);
            // return view('admin.moodle.failed-jobs', compact('failedJobs'));
        })->name('failedJobs');
        
        // Retry all failed Moodle jobs
        Route::post('/retry-failed', function() {
            \Artisan::call('queue:retry', ['id' => 'all']);
            return back()->with('success', 'Retrying all failed jobs');
        })->name('retryFailed');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback Route (Must be last)
|--------------------------------------------------------------------------
*/

// Catch all undefined routes and show 404
Route::fallback(function () {
    return view('errors.404');
});
