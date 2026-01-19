<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Course;
use App\Services\MoodleService;
use App\Services\MoodleClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class MoodleServiceTest extends TestCase
{
    use RefreshDatabase;

    private MoodleService $moodleService;
    private $mockClient;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the MoodleClient
        $this->mockClient = Mockery::mock(MoodleClient::class);
        $this->app->instance(MoodleClient::class, $this->mockClient);

        $this->moodleService = app(MoodleService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_test_moodle_connection(): void
    {
        $this->mockClient
            ->shouldReceive('call')
            ->with('core_webservice_get_site_info')
            ->once()
            ->andReturn(['sitename' => 'Test Moodle']);

        $result = $this->moodleService->testConnection();

        $this->assertTrue($result);
    }

    /** @test */
    public function it_returns_false_when_moodle_connection_fails(): void
    {
        $this->mockClient
            ->shouldReceive('call')
            ->with('core_webservice_get_site_info')
            ->once()
            ->andThrow(new \Exception('Connection failed'));

        $result = $this->moodleService->testConnection();

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_find_user_by_email_in_moodle(): void
    {
        $email = 'test@example.com';

        $this->mockClient
            ->shouldReceive('call')
            ->with('core_user_get_users', Mockery::any())
            ->once()
            ->andReturn([
                'users' => [
                    ['id' => 123, 'email' => $email, 'username' => 'testuser']
                ]
            ]);

        $result = $this->moodleService->findUserByEmail($email);

        $this->assertNotNull($result);
        $this->assertEquals(123, $result['id']);
    }

    /** @test */
    public function it_returns_null_when_user_not_found_in_moodle(): void
    {
        $email = 'notfound@example.com';

        $this->mockClient
            ->shouldReceive('call')
            ->with('core_user_get_users', Mockery::any())
            ->once()
            ->andReturn(['users' => []]);

        $result = $this->moodleService->findUserByEmail($email);

        $this->assertNull($result);
    }

    /** @test */
    public function it_generates_valid_moodle_username(): void
    {
        $email = 'John.Doe@example.com';

        $username = $this->invokeMethod($this->moodleService, 'generateMoodleUsername', [$email]);

        $this->assertIsString($username);
        $this->assertEquals(strtolower($username), $username); // Should be lowercase
        $this->assertStringNotContainsString('@', $username);
    }

    /** @test */
    public function it_validates_course_has_moodle_integration(): void
    {
        $courseWithMoodle = Course::factory()->create([
            'moodle_course_id' => 123,
        ]);

        $courseWithoutMoodle = Course::factory()->create([
            'moodle_course_id' => null,
        ]);

        $this->assertTrue($courseWithMoodle->hasMoodleIntegration());
        $this->assertFalse($courseWithoutMoodle->hasMoodleIntegration());
    }

    /**
     * Helper to invoke private methods for testing.
     */
    private function invokeMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
