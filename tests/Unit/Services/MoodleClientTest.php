<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\MoodleException;
use App\Services\MoodleClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MoodleClientTest extends TestCase
{
    private MoodleClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        config([
            'moodle.base_url' => 'learnabouthealth.hin.gov.tt',
            'moodle.token' => '567e4cb1482a56a1540a5b759b6a1b51',
            'moodle.format' => 'json',
        ]);
        
        $this->client = new MoodleClient();
    }

    public function test_successful_api_call(): void
    {
        Http::fake([
            'moodle.test/*' => Http::response([
                ['id' => 123, 'username' => 'testuser'],
            ], 200),
        ]);

        $result = $this->client->call('core_user_get_users_by_field', [
            'field' => 'email',
            'values' => ['test@example.com'],
        ]);

        $this->assertEquals(123, $result[0]['id']);
        $this->assertEquals('testuser', $result[0]['username']);
    }

    public function test_moodle_exception_thrown_on_error_response(): void
    {
        Http::fake([
            'moodle.test/*' => Http::response([
                'exception' => 'webservice_access_exception',
                'message' => 'Access denied',
                'errorcode' => 'accessdenied',
            ], 200),
        ]);

        $this->expectException(MoodleException::class);
        $this->expectExceptionMessage('Access denied');

        $this->client->call('core_user_create_users');
    }

    public function test_http_exception_thrown_on_connection_error(): void
    {
        Http::fake([
            'moodle.test/*' => Http::response(null, 500),
        ]);

        $this->expectException(MoodleException::class);
        $this->expectExceptionMessage('Failed to communicate with Moodle');

        $this->client->call('core_user_create_users');
    }
}