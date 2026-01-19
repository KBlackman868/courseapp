<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name'     => $this->faker->firstName(),
            'last_name'      => $this->faker->lastName(),
            'email'          => $this->faker->unique()->safeEmail(),
            'department'     => $this->faker->randomElement(['ICT', 'HR', 'Finance', 'Marketing']),
            'email_verified_at' => now(),
            'password'       => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'user_type'      => 'external',
            'is_suspended'   => false,
            'otp_verified'   => false,
            'initial_otp_completed' => false,
            'otp_code'       => null,
            'otp_expires_at' => null,
            'otp_attempts'   => 0,
            'otp_resend_count' => 0,
            'moodle_user_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an internal MOH user.
     */
    public function internal(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $this->faker->unique()->userName() . '@moh.gov.jm',
            'user_type' => 'internal',
        ]);
    }

    /**
     * Indicate that the user has completed OTP verification.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
            'otp_verified' => true,
            'initial_otp_completed' => true,
        ]);
    }

    /**
     * Indicate that the user is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_suspended' => true,
        ]);
    }

    /**
     * Indicate that the user has a Moodle account.
     */
    public function withMoodle(): static
    {
        return $this->state(fn (array $attributes) => [
            'moodle_user_id' => $this->faker->numberBetween(1, 10000),
        ]);
    }
}
