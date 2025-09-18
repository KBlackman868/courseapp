<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MoodleUserController;
use App\Http\Controllers\Api\V1\MoodleEnrolmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
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
        
        // Optional: Bulk operations (if you implement these methods)
        // Route::post('users/bulk', [MoodleUserController::class, 'bulkCreate'])
        //     ->name('api.v1.moodle.users.bulk');
        
        // Route::post('enrolments/bulk', [MoodleEnrolmentController::class, 'bulkStore'])
        //     ->name('api.v1.moodle.enrolments.bulk');
    });
});