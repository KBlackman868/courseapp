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
    private int $connectTimeout;
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
        $this->timeout = (int) config('moodle.timeout', 30);
        $this->connectTimeout = (int) config('moodle.connect_timeout', 15);
        $this->retryTimes = (int) config('moodle.retry_times', 3);
        $this->retrySleep = (int) config('moodle.retry_sleep', 1000);
    }

    /**
     * Ensure Moodle is configured before making API calls.
     *
     * @throws \RuntimeException
     */
    private function ensureConfigured(): void
    {
        if (!$this->baseUrl) {
            throw new \RuntimeException('Moodle base URL is not configured. Please set MOODLE_BASE_URL in your .env file.');
        }

        if (!$this->token) {
            throw new \RuntimeException('Moodle token is not configured. Please set MOODLE_TOKEN in your .env file.');
        }
    }

    /**
     * Call a Moodle Web Service function
     *
     * @param string $function The wsfunction to call
     * @param array $params Additional parameters for the function
     * @return array The response data
     * @throws MoodleException
     */
    public function call(string $function, array $params = []): ?array
    {
        $this->ensureConfigured();

        $correlationId = Str::uuid()->toString();
        
        // Ensure we're using HTTPS
        if (!str_starts_with($this->baseUrl, 'https://')) {
            Log::warning('Moodle URL should use HTTPS', ['url' => $this->baseUrl]);
        }
        
        $endpoint = $this->baseUrl . '/webservice/rest/server.php';
        
        Log::info('Moodle API call initiated', [
            'correlation_id' => $correlationId,
            'function' => $function,
            'endpoint' => $endpoint,
            'params' => $this->sanitizeForLogging($params),
        ]);

        $data = array_merge([
            'wstoken' => $this->token,
            'wsfunction' => $function,
            'moodlewsrestformat' => $this->format,
        ], $params);

        try {
            // Build the HTTP client with proper SSL handling and exponential backoff
            $retrySleep = $this->retrySleep;
            $httpClient = Http::asForm()
                ->timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryTimes, $retrySleep, function (\Exception $exception, $request) {
                    // Retry on connection timeouts and server errors
                    $message = $exception->getMessage();
                    return str_contains($message, 'cURL error 28')
                        || str_contains($message, 'timed out')
                        || str_contains($message, 'cURL error 7')
                        || str_contains($message, 'cURL error 35')
                        || ($exception instanceof RequestException && $exception->response?->serverError());
                }, throw: false);

            // Check if we should verify SSL (disable only for self-signed certificates)
            if (config('moodle.verify_ssl', true) === false) {
                $httpClient = $httpClient->withoutVerifying();
            }

            // Add explicit options for HTTPS
            $httpClient = $httpClient->withOptions([
                'verify' => config('moodle.verify_ssl', true),
                'protocols' => ['https'], // Force HTTPS only
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => config('moodle.verify_ssl', true),
                    CURLOPT_SSL_VERIFYHOST => config('moodle.verify_ssl', true) ? 2 : 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 5,
                    CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
                    CURLOPT_TIMEOUT => $this->timeout,
                ]
            ]);
            
            $response = $httpClient->post($endpoint, $data);

            // Check if response is successful
            if (!$response->successful()) {
                $body = $response->body();

                Log::error('Moodle API HTTP error', [
                    'correlation_id' => $correlationId,
                    'function' => $function,
                    'status' => $response->status(),
                    'body' => $body,
                ]);

                // Detect access control exceptions in HTTP error responses
                if (str_contains($body, 'accessexception') || str_contains($body, 'Access control exception')) {
                    throw new MoodleException(
                        "Moodle access denied for function '{$function}'. "
                        . "The web service token does not have permission to call this function. "
                        . "A Moodle administrator must add '{$function}' to the external service linked to the API token "
                        . "under Site Administration > Server > Web services > External services.",
                        'accessexception'
                    );
                }

                throw new MoodleException(
                    'HTTP Error: ' . $response->status() . ' - ' . $body,
                    'http_error'
                );
            }

            $result = $response->json();

            // Check for Moodle exceptions in response
            if (isset($result['exception'])) {
                $errorCode = $result['errorcode'] ?? 'unknown_error';
                $message = $result['message'] ?? 'Moodle API error occurred';
                $debugInfo = $result['debuginfo'] ?? null;

                Log::error('Moodle API returned exception', [
                    'correlation_id' => $correlationId,
                    'function' => $function,
                    'exception' => $result['exception'],
                    'message' => $message,
                    'errorcode' => $errorCode,
                    'debuginfo' => $debugInfo,
                ]);

                throw new MoodleException(
                    $result['message'] ?? 'Moodle API error occurred',
                    $result['errorcode'] ?? 'unknown_error'
                );
            }

            // Check for warnings (non-fatal but should be logged)
            if (isset($result['warnings']) && !empty($result['warnings'])) {
                Log::warning('Moodle API returned warnings', [
                    'correlation_id' => $correlationId,
                    'function' => $function,
                    'warnings' => $result['warnings'],
                ]);
            }

            Log::info('Moodle API call successful', [
                'correlation_id' => $correlationId,
                'function' => $function,
                'result_count' => is_array($result) ? count($result) : 'non-array',
            ]);

            if ($result === null) {
                return [];
            }

            return $result;

        } catch (RequestException $e) {
            $responseBody = $e->response ? $e->response->body() : 'No response body';

            Log::error('Moodle API HTTP error', [
                'correlation_id' => $correlationId,
                'function' => $function,
                'status' => $e->response?->status(),
                'message' => $e->getMessage(),
                'response_body' => $responseBody,
                'trace' => $e->getTraceAsString(),
            ]);

            // Check if it's a timeout issue
            if (str_contains($e->getMessage(), 'cURL error 28') || str_contains($e->getMessage(), 'timed out')) {
                throw new MoodleException(
                    'Connection timeout to Moodle. Please check network connectivity and SSL configuration.',
                    'timeout_error'
                );
            }

            // Detect access control exceptions in HTTP error responses
            if (str_contains($responseBody, 'accessexception') || str_contains($responseBody, 'Access control exception')) {
                throw new MoodleException(
                    "Moodle access denied for function '{$function}'. "
                    . "The web service token does not have permission to call this function. "
                    . "A Moodle administrator must add '{$function}' to the external service linked to the API token "
                    . "under Site Administration > Server > Web services > External services.",
                    'accessexception'
                );
            }

            throw new MoodleException(
                'Failed to communicate with Moodle: ' . $e->getMessage(),
                'http_error'
            );
        } catch (\Exception $e) {
            Log::error('Unexpected error in Moodle API call', [
                'correlation_id' => $correlationId,
                'function' => $function,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new MoodleException(
                'Unexpected error: ' . $e->getMessage(),
                'unexpected_error'
            );
        }
    }

    /**
     * Test the connection to Moodle
     */
    public function testConnection(): bool
    {
        try {
            $result = $this->call('core_webservice_get_site_info');
            return isset($result['sitename']);
        } catch (\Exception $e) {
            Log::error('Moodle connection test failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Sanitize data for logging (remove sensitive information)
     */
    private function sanitizeForLogging(array $data): array
    {
        $sanitized = $data;
        
        // Remove or mask sensitive fields
        $sensitiveFields = ['password', 'email', 'firstname', 'lastname', 'wstoken'];
        
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