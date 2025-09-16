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
    private bool $verifySSL;

    public function __construct()
    {
        $baseUrl = config('moodle.base_url');
        if (!$baseUrl) {
            throw new \RuntimeException('Moodle base URL is not configured.');
        }
        
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = config('moodle.token', '');
        
        if (!$this->token) {
            throw new \RuntimeException('Moodle token is not configured.');
        }
        
        // Cast config values to proper types
        $this->format = config('moodle.format', 'json');
        $this->timeout = (int) config('moodle.timeout', 20);
        $this->retryTimes = (int) config('moodle.retry_times', 2);
        $this->retrySleep = (int) config('moodle.retry_sleep', 200);
        $this->verifySSL = (bool) config('moodle.verify_ssl', true);
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
        
        // Build the complete data array
        $data = array_merge([
            'wstoken' => $this->token,
            'wsfunction' => $function,
            'moodlewsrestformat' => $this->format,
        ], $params);
        
        // Log the request for debugging (hide sensitive data in production)
        Log::info('Moodle API call initiated', [
            'correlation_id' => $correlationId,
            'function' => $function,
            'url' => $this->baseUrl . '/webservice/rest/server.php',
            'verify_ssl' => $this->verifySSL,
        ]);

        try {
            // Build HTTP client with SSL verification setting
            $httpClient = Http::asForm()
                ->timeout($this->timeout)
                ->retry($this->retryTimes, $this->retrySleep);
            
            // Disable SSL verification if configured (for dev/internal environments only!)
            if (!$this->verifySSL) {
                $httpClient = $httpClient->withOptions([
                    'verify' => false,
                    'allow_redirects' => true,
                ]);
                
                Log::warning('SSL verification is disabled for Moodle API calls', [
                    'correlation_id' => $correlationId,
                ]);
            }
            
            $response = $httpClient->post($this->baseUrl . '/webservice/rest/server.php', $data);
            
            // Log raw response status
            Log::debug('Moodle API raw response', [
                'correlation_id' => $correlationId,
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);
            
            if (!$response->successful()) {
                throw new MoodleException(
                    "HTTP request returned status code {$response->status()}: {$response->body()}"
                );
            }
            
            $responseData = $response->json();
            
            // Check for Moodle exception in response
            if (isset($responseData['exception'])) {
                Log::error('Moodle API returned exception', [
                    'correlation_id' => $correlationId,
                    'exception' => $responseData['exception'],
                    'errorcode' => $responseData['errorcode'] ?? null,
                    'message' => $responseData['message'] ?? null,
                    'debuginfo' => $responseData['debuginfo'] ?? null,
                ]);
                
                throw new MoodleException(
                    "Moodle exception: " . ($responseData['message'] ?? 'Unknown error') . 
                    " Debug: " . ($responseData['debuginfo'] ?? 'No debug info')
                );
            }
            
            // Check for error in response
            if (isset($responseData['error'])) {
                throw new MoodleException(
                    "Moodle error: " . $responseData['error']
                );
            }
            
            Log::info('Moodle API call successful', [
                'correlation_id' => $correlationId,
                'function' => $function,
            ]);
            
            return $responseData;
            
        } catch (RequestException $e) {
            Log::error('Moodle API request failed', [
                'correlation_id' => $correlationId,
                'function' => $function,
                'error' => $e->getMessage(),
            ]);
            
            throw new MoodleException(
                "Failed to communicate with Moodle: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
    
    /**
     * Test the connection to Moodle
     * 
     * @return array Site info if successful
     * @throws MoodleException
     */
    public function testConnection(): array
    {
        return $this->call('core_webservice_get_site_info');
    }
    
    /**
     * Get sample params for logging (hide sensitive data)
     */
    private function getSampleParams(array $params): array
    {
        $sample = [];
        foreach ($params as $key => $value) {
            if (str_contains(strtolower($key), 'password')) {
                $sample[$key] = '***HIDDEN***';
            } elseif (is_string($value) && strlen($value) > 50) {
                $sample[$key] = substr($value, 0, 50) . '...';
            } else {
                $sample[$key] = $value;
            }
        }
        return $sample;
    }
}