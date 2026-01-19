<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpServiceTest extends TestCase
{
    use RefreshDatabase;

    private OtpService $otpService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->otpService = app(OtpService::class);
        Mail::fake();
    }

    /** @test */
    public function it_generates_6_digit_otp_code(): void
    {
        $code = $this->invokeMethod($this->otpService, 'generateOtpCode');

        $this->assertIsString($code);
        $this->assertEquals(6, strlen($code));
        $this->assertTrue(ctype_digit($code));
    }

    /** @test */
    public function it_determines_new_user_needs_otp_verification(): void
    {
        $user = User::factory()->create([
            'initial_otp_completed' => false,
            'otp_verified' => false,
        ]);

        $this->assertTrue($this->otpService->needsOtpVerification($user));
    }

    /** @test */
    public function it_determines_verified_user_does_not_need_otp(): void
    {
        $user = User::factory()->create([
            'initial_otp_completed' => true,
            'otp_verified' => true,
        ]);

        $this->assertFalse($this->otpService->needsOtpVerification($user));
    }

    /** @test */
    public function it_can_send_otp_to_user(): void
    {
        $user = User::factory()->create();

        $result = $this->otpService->sendOtp($user);

        $this->assertTrue($result['success']);
        $user->refresh();
        $this->assertNotNull($user->otp_code);
        $this->assertNotNull($user->otp_expires_at);
    }

    /** @test */
    public function it_verifies_valid_otp_code(): void
    {
        $user = User::factory()->create([
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
        ]);

        $result = $this->otpService->verifyOtp($user, '123456');

        $this->assertTrue($result['success']);
        $user->refresh();
        $this->assertTrue($user->otp_verified);
    }

    /** @test */
    public function it_rejects_invalid_otp_code(): void
    {
        $user = User::factory()->create([
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
        ]);

        $result = $this->otpService->verifyOtp($user, '654321');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('invalid', strtolower($result['message']));
    }

    /** @test */
    public function it_rejects_expired_otp_code(): void
    {
        $user = User::factory()->create([
            'otp_code' => '123456',
            'otp_expires_at' => now()->subMinutes(5),
            'otp_attempts' => 0,
        ]);

        $result = $this->otpService->verifyOtp($user, '123456');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('expired', strtolower($result['message']));
    }

    /** @test */
    public function it_tracks_remaining_resend_attempts(): void
    {
        $user = User::factory()->create([
            'otp_resend_count' => 2,
        ]);

        $remaining = $this->otpService->getRemainingResends($user);

        $this->assertIsInt($remaining);
        $this->assertGreaterThanOrEqual(0, $remaining);
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
