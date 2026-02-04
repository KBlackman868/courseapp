<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MoodleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class MoodleTestController extends Controller
{
    protected $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    /**
     * Show the Moodle test dashboard
     */
    public function index()
    {
        return view('admin.moodle-test');
    }

    /**
     * Test Moodle connection
     */
    public function testConnection()
    {
        $url = config('services.moodle.url', env('MOODLE_URL'));
        $token = config('services.moodle.token', env('MOODLE_TOKEN'));
        
        if (empty($url) || empty($token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Moodle URL or Token not configured',
                'url_configured' => !empty($url),
                'token_configured' => !empty($token),
                'moodle_url' => $url
            ]);
        }
        
        try {
            if ($this->moodleService->testConnection()) {
                // Try to get site info for more details
                $siteInfo = $this->moodleService->call('core_webservice_get_site_info');
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Connected to Moodle successfully!',
                    'moodle_url' => $url,
                    'site_name' => $siteInfo['sitename'] ?? 'Unknown',
                    'moodle_version' => $siteInfo['release'] ?? 'Unknown'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Could not connect to Moodle',
                    'moodle_url' => $url
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Moodle connection test failed', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Connection failed',
                'error' => $e->getMessage(),
                'moodle_url' => $url
            ]);
        }
    }

    /**
     * Create a test user in both systems
     */
    public function createTestUser(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        try {
            // Create user in Laravel
            $user = User::create([
                'first_name' => $request->firstname,
                'last_name' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'department' => 'Test Department',
                'organization' => 'Test Organization',
            ]);
            
            $user->assignRole('user');
            
            // Try to sync to Moodle
            $moodleUserId = null;
            try {
                $userData = [
                    'username' => strtolower(str_replace('@', '_', $request->email)),
                    'password' => $request->password,
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'auth' => 'manual',
                    'department' => 'Test Department',
                    'description' => 'Test user from admin dashboard',
                    'city' => 'Test City',
                    'country' => 'TT'
                ];
                
                $moodleUserId = $this->moodleService->createUser($userData);
                
                if ($moodleUserId) {
                    $user->moodle_user_id = $moodleUserId;
                    $user->save();
                }
            } catch (\Exception $e) {
                Log::error('Moodle sync failed for test user', [
                    'error' => $e->getMessage()
                ]);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'laravel_id' => $user->id,
                'moodle_id' => $moodleUserId,
                'email' => $user->email
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Sync an existing user to Moodle
     */
    public function syncUser($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->moodle_user_id) {
            return response()->json([
                'status' => 'info',
                'message' => 'User already synced to Moodle'
            ]);
        }
        
        try {
            // Generate a temporary password for Moodle
            $tempPassword = 'Temp@' . rand(100000, 999999);
            
            $userData = [
                'username' => strtolower(str_replace('@', '_', $user->email)),
                'password' => $tempPassword,
                'firstname' => $user->first_name,
                'lastname' => $user->last_name,
                'email' => $user->email,
                'auth' => 'manual',
                'department' => $user->department ?? 'Unknown',
                'description' => 'User synced from Laravel',
                'city' => 'Trinidad and Tobago',
                'country' => 'TT'
            ];
            
            $moodleUserId = $this->moodleService->createUser($userData);
            
            if ($moodleUserId) {
                $user->moodle_user_id = $moodleUserId;
                $user->save();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'User synced successfully! Temporary password: ' . $tempPassword,
                    'moodle_id' => $moodleUserId
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create user in Moodle'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get recent Moodle-related logs
     */
    public function getLogs()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (!File::exists($logFile)) {
                return response()->json(['logs' => 'Log file not found']);
            }
            
            // Read last 50 lines that contain "Moodle"
            $lines = [];
            $file = new \SplFileObject($logFile);
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();
            
            $startLine = max(0, $totalLines - 500); // Check last 500 lines
            $file->seek($startLine);
            
            while (!$file->eof()) {
                $line = $file->fgets();
                if (stripos($line, 'moodle') !== false) {
                    $lines[] = $line;
                }
            }
            
            // Get last 20 Moodle-related lines
            $lines = array_slice($lines, -20);
            
            return response()->json([
                'logs' => implode("\n", $lines) ?: 'No Moodle-related logs found'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'logs' => 'Error reading logs: ' . $e->getMessage()
            ]);
        }
    }
}