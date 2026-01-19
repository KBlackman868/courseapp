<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HealthCheckController extends Controller
{
    /**
     * Basic health check for load balancers.
     * Returns 200 if the application is running.
     */
    public function ping(): JsonResponse
    {
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Comprehensive health check for monitoring.
     * Checks database, cache, and external services.
     */
    public function health(): JsonResponse
    {
        $checks = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'checks' => [],
        ];

        // Check database connection
        $checks['checks']['database'] = $this->checkDatabase();

        // Check cache
        $checks['checks']['cache'] = $this->checkCache();

        // Check queue (if using database driver)
        $checks['checks']['queue'] = $this->checkQueue();

        // Check Moodle connectivity (optional)
        $checks['checks']['moodle'] = $this->checkMoodle();

        // Determine overall status
        $hasFailure = collect($checks['checks'])
            ->contains(fn($check) => $check['status'] === 'unhealthy');

        $checks['status'] = $hasFailure ? 'unhealthy' : 'healthy';

        $statusCode = $hasFailure ? 503 : 200;

        return response()->json($checks, $statusCode);
    }

    /**
     * Check database connectivity.
     */
    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $duration = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'healthy',
                'response_time_ms' => $duration,
            ];
        } catch (\Exception $e) {
            Log::error('Health check: Database connection failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'unhealthy',
                'error' => 'Database connection failed',
            ];
        }
    }

    /**
     * Check cache connectivity.
     */
    private function checkCache(): array
    {
        try {
            $key = 'health_check_' . uniqid();
            $value = 'test_value';

            Cache::put($key, $value, 10);
            $retrieved = Cache::get($key);
            Cache::forget($key);

            if ($retrieved !== $value) {
                throw new \Exception('Cache read/write mismatch');
            }

            return [
                'status' => 'healthy',
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            Log::error('Health check: Cache connection failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'unhealthy',
                'error' => 'Cache connection failed',
            ];
        }
    }

    /**
     * Check queue health.
     */
    private function checkQueue(): array
    {
        try {
            $driver = config('queue.default');

            if ($driver === 'database') {
                $pendingJobs = DB::table('jobs')->count();
                $failedJobs = DB::table('failed_jobs')->count();

                return [
                    'status' => 'healthy',
                    'driver' => $driver,
                    'pending_jobs' => $pendingJobs,
                    'failed_jobs' => $failedJobs,
                ];
            }

            return [
                'status' => 'healthy',
                'driver' => $driver,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => 'Queue check failed',
            ];
        }
    }

    /**
     * Check Moodle connectivity (optional).
     */
    private function checkMoodle(): array
    {
        if (!config('moodle.base_url') || !config('moodle.token')) {
            return [
                'status' => 'skipped',
                'message' => 'Moodle not configured',
            ];
        }

        try {
            $moodleService = app(\App\Services\MoodleService::class);
            $connected = $moodleService->testConnection();

            return [
                'status' => $connected ? 'healthy' : 'unhealthy',
                'connected' => $connected,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => 'Moodle connection failed',
            ];
        }
    }
}
