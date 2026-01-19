<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'student']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'superadmin']);
    }

    /** @test */
    public function guest_cannot_enroll_in_course(): void
    {
        $course = Course::factory()->create(['status' => 'active']);

        $response = $this->post(route('courses.enroll.store', $course));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_enroll_in_course(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'initial_otp_completed' => true,
        ]);
        $user->assignRole('student');

        $course = Course::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)
            ->post(route('courses.enroll.store', $course));

        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    /** @test */
    public function user_cannot_enroll_twice_in_same_course(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'initial_otp_completed' => true,
        ]);
        $user->assignRole('student');

        $course = Course::factory()->create(['status' => 'active']);

        // First enrollment
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
        ]);

        // Try to enroll again
        $response = $this->actingAs($user)
            ->post(route('courses.enroll.store', $course));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Should still only have one enrollment
        $this->assertEquals(1, Enrollment::where([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ])->count());
    }

    /** @test */
    public function internal_moh_user_is_auto_approved(): void
    {
        $user = User::factory()->create([
            'email' => 'test@moh.gov.jm',
            'user_type' => 'internal',
            'email_verified_at' => now(),
            'initial_otp_completed' => true,
        ]);
        $user->assignRole('student');

        $course = Course::factory()->create(['status' => 'active']);

        $this->actingAs($user)
            ->post(route('courses.enroll.store', $course));

        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function external_user_enrollment_is_pending(): void
    {
        $user = User::factory()->create([
            'email' => 'test@gmail.com',
            'user_type' => 'external',
            'email_verified_at' => now(),
            'initial_otp_completed' => true,
        ]);
        $user->assignRole('student');

        $course = Course::factory()->create(['status' => 'active']);

        $this->actingAs($user)
            ->post(route('courses.enroll.store', $course));

        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function admin_can_approve_enrollment(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'initial_otp_completed' => true,
        ]);
        $admin->assignRole('admin');

        $enrollment = Enrollment::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->post(route('admin.enrollments.approve', $enrollment->id));

        $enrollment->refresh();
        $this->assertEquals('approved', $enrollment->status);
    }

    /** @test */
    public function admin_can_deny_enrollment(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'initial_otp_completed' => true,
        ]);
        $admin->assignRole('admin');

        $enrollment = Enrollment::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->post(route('admin.enrollments.deny', $enrollment->id));

        $enrollment->refresh();
        $this->assertEquals('denied', $enrollment->status);
    }

    /** @test */
    public function non_admin_cannot_approve_enrollment(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'initial_otp_completed' => true,
        ]);
        $user->assignRole('student');

        $enrollment = Enrollment::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($user)
            ->post(route('admin.enrollments.approve', $enrollment->id));

        $response->assertStatus(403);
        $enrollment->refresh();
        $this->assertEquals('pending', $enrollment->status);
    }
}
