<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(['active', 'inactive', 'draft']),
            'image' => null,
            'category_id' => null,
            'creator_id' => User::factory(),
            'moodle_course_id' => null,
            'moodle_course_shortname' => null,
        ];
    }

    /**
     * Indicate that the course is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the course is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the course has Moodle integration.
     */
    public function withMoodle(): static
    {
        return $this->state(fn (array $attributes) => [
            'moodle_course_id' => $this->faker->numberBetween(1, 1000),
            'moodle_course_shortname' => strtoupper($this->faker->lexify('???-???')),
        ]);
    }

    /**
     * Indicate that the course belongs to a category.
     */
    public function withCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => Category::factory(),
        ]);
    }
}
