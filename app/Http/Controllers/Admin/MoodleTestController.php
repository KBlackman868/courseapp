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
        $url = config('moodle.base_url');
        $token = config('moodle.token');

        if (empty($url) || empty($token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Moodle URL or Token not configured',
                'url_configured' => !empty($url),
                'token_configured' => !empty($token),
                'moodle_url' => $url
            ]);
        }

        // Run network diagnostics before attempting API call
        $diagnostics = $this->runDiagnostics($url);

        try {
            if ($this->moodleService->testConnection()) {
                $siteInfo = $this->moodleService->call('core_webservice_get_site_info');

                return response()->json([
                    'status' => 'success',
                    'message' => 'Connected to Moodle successfully!',
                    'moodle_url' => $url,
                    'site_name' => $siteInfo['sitename'] ?? 'Unknown',
                    'moodle_version' => $siteInfo['release'] ?? 'Unknown',
                    'diagnostics' => $diagnostics,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Could not connect to Moodle',
                    'moodle_url' => $url,
                    'diagnostics' => $diagnostics,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Moodle connection test failed', [
                'error' => $e->getMessage(),
                'diagnostics' => $diagnostics,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Connection failed: ' . $e->getMessage(),
                'moodle_url' => $url,
                'diagnostics' => $diagnostics,
            ]);
        }
    }

    /**
     * Run network-level diagnostics against the Moodle host
     */
    private function runDiagnostics(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST);
        $diagnostics = [
            'host' => $host,
            'dns_resolves' => false,
            'resolved_ip' => null,
            'port_443_open' => false,
            'ssl_valid' => false,
            'timeout_settings' => [
                'connect_timeout' => config('moodle.connect_timeout', 15) . 's',
                'transfer_timeout' => config('moodle.timeout', 30) . 's',
                'retry_times' => config('moodle.retry_times', 3),
                'retry_sleep' => config('moodle.retry_sleep', 1000) . 'ms',
            ],
        ];

        // Step 1: DNS resolution
        $ip = @gethostbyname($host);
        if ($ip !== $host) {
            $diagnostics['dns_resolves'] = true;
            $diagnostics['resolved_ip'] = $ip;
        } else {
            $diagnostics['dns_error'] = 'Could not resolve hostname';
            return $diagnostics;
        }

        // Step 2: TCP port 443 connectivity (5s timeout)
        $socket = @fsockopen($host, 443, $errno, $errstr, 5);
        if ($socket) {
            $diagnostics['port_443_open'] = true;
            fclose($socket);
        } else {
            $diagnostics['port_443_error'] = "Connection refused or timed out: [$errno] $errstr";
            return $diagnostics;
        }

        // Step 3: SSL handshake check
        $context = stream_context_create(['ssl' => [
            'capture_peer_cert' => true,
            'verify_peer' => true,
            'verify_peer_name' => true,
        ]]);
        $sslSocket = @stream_socket_client(
            "ssl://{$host}:443",
            $sslErrno,
            $sslErrstr,
            5,
            STREAM_CLIENT_CONNECT,
            $context
        );
        if ($sslSocket) {
            $diagnostics['ssl_valid'] = true;
            fclose($sslSocket);
        } else {
            $diagnostics['ssl_error'] = "SSL handshake failed: [$sslErrno] $sslErrstr";
        }

        return $diagnostics;
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
                'department' => 'Test Department'
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