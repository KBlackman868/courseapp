<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\MoodleException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MoodleClient
{
    private string $baseUrl;
    private string $token;
    private string $format;
    private int $timeout;
    private int $retryTimes;
    private int $retrySleep;

    public function __construct()
    {
        $baseUrl = config('moodle.base_url');
        if (!$baseUrl) {
            throw new \RuntimeException('Moodle base URL is not configured. Please set MOODLE_BASE_URL in your .env file.');
        }
        
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = config('moodle.token', '');
        
        if (!$this->token) {
            throw new \RuntimeException('Moodle token is not configured. Please set MOODLE_TOKEN in your .env file.');
        }
        
        $this->format = config('moodle.format', 'json');
        
        // Cast to integers to ensure type compatibility
        $this->timeout = (int) config('moodle.timeout', 20);
        $this->retryTimes = (int) config('moodle.retry_times', 2);
        $this->retrySleep = (int) config('moodle.retry_sleep', 200);
    }

    /**
     * Call a Moodle Web Service function
     *
     * @param string $function The wsfunction to call
     * @param array $params Additional parameters for the function
     * @return array The response data
     * @throws MoodleException
     */
    public function call(string $function, array $params = []): array
    {
        $correlationId = Str::uuid()->toString();
        
        Log::info('Moodle API call initiated', [
            'correlation_id' => $correlationId,
            'function' => $function,
            'params' => $this->sanitizeForLogging($params),
        ]);

        $data = array_merge([
            'wstoken' => $this->token,
            'wsfunction' => $function,
            'moodlewsrestformat' => $this->format,
        ], $params);

        try {
            $response = Http::asForm()
                ->withoutVerifying()  // Bypass SSL verification for self-signed certificate
                ->timeout($this->timeout)
                ->retry($this->retryTimes, $this->retrySleep)
                ->post($this->baseUrl . '/webservice/rest/server.php', $data);

            $response->throw();

            $result = $response->json();

            // Check for Moodle exceptions in response
            if (isset($result['exception'])) {
                Log::error('Moodle API returned exception', [
                    'correlation_id' => $correlationId,
                    'function' => $function,
                    'exception' => $result['exception'],
                    'message' => $result['message'] ?? 'Unknown error',
                ]);

                throw new MoodleException(
                    $result['message'] ?? 'Moodle API error occurred',
                    $result['errorcode'] ?? 'unknown_error'
                );
            }

            Log::info('Moodle API call successful', [
                'correlation_id' => $correlationId,
                'function' => $function,
            ]);

            return $result;

        } catch (RequestException $e) {
            Log::error('Moodle API HTTP error', [
                'correlation_id' => $correlationId,
                'function' => $function,
                'status' => $e->response?->status(),
                'message' => $e->getMessage(),
            ]);

            throw new MoodleException(
                'Failed to communicate with Moodle: ' . $e->getMessage(),
                'http_error'
            );
        }
    }

    /**
     * Sanitize data for logging (remove sensitive information)
     */
    private function sanitizeForLogging(array $data): array
    {
        $sanitized = $data;
        
        // Remove or mask sensitive fields
        $sensitiveFields = ['password', 'email', 'firstname', 'lastname'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = '[REDACTED]';
            }
            
            // Check nested arrays (like users[0][password])
            foreach ($sanitized as $key => $value) {
                if (is_array($value)) {
                    $sanitized[$key] = $this->sanitizeForLogging($value);
                }
            }
        }
        
        return $sanitized;
    }
}