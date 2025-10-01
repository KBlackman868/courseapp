<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Services\MoodleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class MoodleIntegrationController extends Controller
{
    private MoodleClient $moodleClient;
    
    public function __construct(MoodleClient $moodleClient)
    {
        $this->moodleClient = $moodleClient;
    }
    
    /**
     * Show Moodle integration status dashboard
     */
    public function status()
    {
        $stats = [
            'users_total' => User::count(),
            'users_with_moodle' => User::whereNotNull('moodle_user_id')->count(),
            'courses_total' => \App\Models\Course::count(),
            'courses_with_moodle' => \App\Models\Course::whereNotNull('moodle_course_id')->count(),
            'pending_sync' => \App\Models\Enrollment::where('status', 'approved')
                ->whereHas('user', fn($q) => $q->whereNull('moodle_user_id'))
                ->count(),
            'failed_jobs' => DB::table('failed_jobs')->count(),
        ];
        
        // Check if view exists, otherwise return JSON
        if (view()->exists('admin.moodle.status')) {
            return view('admin.moodle.status', compact('stats'));
        }
        
        return response()->json($stats);
    }
    
    /**
     * Test Moodle API connection
     */
    public function testConnection()
    {
        try {
            $result = $this->moodleClient->call('core_webservice_get_site_info');
            
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
                'moodle_url' => config('moodle.base_url'),
                'token_configured' => !empty(config('moodle.token'))
            ], 500);
        }
    }
    
    /**
     * View failed Moodle sync jobs
     */
    public function failedJobs()
    {
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->paginate(20);
        
        if (view()->exists('admin.moodle.failed-jobs')) {
            return view('admin.moodle.failed-jobs', compact('failedJobs'));
        }
        
        return response()->json($failedJobs);
    }
    
    /**
     * Retry all failed jobs
     */
    public function retryFailed()
    {
        Artisan::call('queue:retry', ['id' => 'all']);
        return back()->with('success', 'Retrying all failed jobs');
    }
    
    /**
     * Sync a single user to Moodle
     */
    public function syncUser(User $user)
    {
        CreateOrLinkMoodleUser::dispatch($user);
        return back()->with('success', 'User sync initiated for ' . $user->email);
    }
    
    /**
     * Bulk sync users to Moodle
     */
    public function bulkSyncUsers()
    {
        $users = User::whereNull('moodle_user_id')->get();
        $count = 0;
        
        foreach ($users as $user) {
            CreateOrLinkMoodleUser::dispatch($user);
            $count++;
        }
        
        return response()->json([
            'status' => 'success',
            'message' => "Initiated sync for $count users"
        ]);
    }
}