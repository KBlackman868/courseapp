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
use App\Http\Controllers\Admin\MoodleTestController;
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

        // Add this to your routes/web.php file

Route::get('/moodle-diagnosis', function () {
    
    $results = [];
    
    // 1. Check ENV variables
    $results['env_check'] = [
        'MOODLE_URL' => env('MOODLE_URL') ? '✅ Set' : '❌ NOT SET',
        'MOODLE_TOKEN' => env('MOODLE_TOKEN') ? '✅ Set' : '❌ NOT SET',
        'actual_url' => env('MOODLE_URL', 'Not configured'),
    ];
    
    // 2. Test connection
    try {
        $moodle = new \App\Services\MoodleService();
        $connected = $moodle->testConnection();
        $results['connection'] = $connected ? '✅ Connected' : '❌ Failed';
    } catch (\Exception $e) {
        $results['connection'] = '❌ Error: ' . $e->getMessage();
    }
    
    // 3. Check available functions
    try {
        $response = $moodle->call('core_webservice_get_site_info');
        $functions = $response['functions'] ?? [];
        
        // Check for critical functions
        $critical = [
            'core_user_create_users' => false,
            'core_user_get_users' => false,
        ];
        
        foreach ($functions as $func) {
            if (isset($critical[$func['name']])) {
                $critical[$func['name']] = true;
            }
        }
        
        $results['permissions'] = [
            'core_user_create_users' => $critical['core_user_create_users'] ? '✅ Enabled' : '❌ DISABLED - Cannot create users!',
            'core_user_get_users' => $critical['core_user_get_users'] ? '✅ Enabled' : '❌ DISABLED - Cannot get users!',
        ];
        
    } catch (\Exception $e) {
        $results['permissions'] = '❌ Could not check permissions: ' . $e->getMessage();
    }
    
    // 4. Test creating a user
    try {
        $testUsername = 'test_' . time();
        $testUser = [
            'username' => $testUsername,
            'password' => 'TestPass123!',
            'firstname' => 'Test',
            'lastname' => 'User',
            'email' => $testUsername . '@test.com',
            'auth' => 'manual',
        ];
        
        $userId = $moodle->createUser($testUser);
        
        if ($userId) {
            $results['user_creation'] = "✅ Test user created successfully! (ID: $userId)";
        } else {
            $results['user_creation'] = "❌ User creation returned null";
        }
        
    } catch (\Exception $e) {
        $results['user_creation'] = "❌ User creation failed: " . $e->getMessage();
    }
    
    // 5. Check last 5 users in Laravel
    $users = \App\Models\User::latest()->take(5)->get(['email', 'moodle_user_id', 'created_at']);
    $results['recent_users'] = $users->map(function ($user) {
        return [
            'email' => $user->email,
            'moodle_synced' => $user->moodle_user_id ? "✅ Yes (ID: {$user->moodle_user_id})" : "❌ No",
            'created' => $user->created_at->diffForHumans(),
        ];
    })->toArray();
    
    // Display results
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <title>Moodle Integration Diagnosis</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 p-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">🔍 Moodle Integration Diagnosis</h1>';
    
    // Environment Check
    $html .= '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">1️⃣ Environment Configuration</h2>';
    foreach ($results['env_check'] as $key => $value) {
        $html .= "<p><strong>$key:</strong> $value</p>";
    }
    $html .= '</div>';
    
    // Connection Test
    $html .= '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">2️⃣ Connection Test</h2>
        <p>' . $results['connection'] . '</p>
    </div>';
    
    // Permissions Check
    $html .= '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">3️⃣ Moodle Permissions</h2>';
    if (is_array($results['permissions'])) {
        foreach ($results['permissions'] as $func => $status) {
            $html .= "<p><strong>$func:</strong> $status</p>";
        }
    } else {
        $html .= '<p>' . $results['permissions'] . '</p>';
    }
    $html .= '</div>';
    
    // User Creation Test
    $html .= '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">4️⃣ User Creation Test</h2>
        <p>' . $results['user_creation'] . '</p>
    </div>';
    
    // Recent Users
    $html .= '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">5️⃣ Recent Laravel Users</h2>
        <table class="w-full">';
    foreach ($results['recent_users'] as $user) {
        $html .= '<tr class="border-b">
            <td class="py-2">' . $user['email'] . '</td>
            <td class="py-2">' . $user['moodle_synced'] . '</td>
            <td class="py-2 text-gray-500">' . $user['created'] . '</td>
        </tr>';
    }
    $html .= '</table>
    </div>';
    
    // Solution
    $html .= '<div class="bg-blue-50 rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">💡 Diagnosis Summary</h2>';
    
    if (strpos($results['connection'], '✅') !== false) {
        if (isset($results['permissions']['core_user_create_users']) && 
            strpos($results['permissions']['core_user_create_users'], '❌') !== false) {
            $html .= '<p class="text-red-600 font-bold">❌ PROBLEM FOUND: Your Moodle token does NOT have permission to create users!</p>
                     <p class="mt-2">Solution: In Moodle, go to Site Administration → Web Services → External Services, 
                     find your service and add the "core_user_create_users" function.</p>';
        } elseif (strpos($results['user_creation'], '❌') !== false) {
            $html .= '<p class="text-red-600 font-bold">❌ PROBLEM: User creation is failing even with permissions.</p>
                     <p class="mt-2">Error: ' . $results['user_creation'] . '</p>';
        } else {
            $html .= '<p class="text-green-600 font-bold">✅ Everything seems to be working!</p>';
        }
    } else {
        $html .= '<p class="text-red-600 font-bold">❌ PROBLEM: Cannot connect to Moodle. Check your MOODLE_URL and MOODLE_TOKEN in .env file.</p>';
    }
    
    $html .= '</div>
        </div>
    </body>
    </html>';
    
    return $html;
})->middleware('auth');
        // Route to list all users
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        // Add this route for admins
        Route::post('/courses/{course}/sync-to-moodle', [CourseController::class, 'syncToMoodle'])
        ->name('courses.syncToMoodle')
        ->middleware(['auth', 'role:admin|superadmin']);
         // Route to update a user's role
        Route::post('/admin/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('admin.users.updateRole');
            // User deletion and suspension routes
            Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])
            ->name('admin.users.destroy');
        Route::patch('/admin/users/{user}/suspend', [UserManagementController::class, 'suspend'])
            ->name('admin.users.suspend');

        Route::patch('/admin/users/{user}/reactivate', [UserManagementController::class, 'reactivate'])
            ->name('admin.users.reactivate');

        Route::delete('/admin/users/bulk-delete', [UserManagementController::class, 'bulkDelete'])
            ->name('admin.users.bulkDelete');
});

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
