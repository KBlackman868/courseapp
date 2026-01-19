<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
            'moodle_category_id' => null,
            'parent_id' => null,
            'idnumber' => $this->faker->unique()->lexify('CAT-????'),
            'sortorder' => $this->faker->numberBetween(1, 100),
            'visible' => true,
        ];
    }

    /**
     * Indicate that the category has Moodle integration.
     */
    public function withMoodle(): static
    {
        return $this->state(fn (array $attributes) => [
            'moodle_category_id' => $this->faker->numberBetween(1, 100),
        ]);
    }

    /**
     * Indicate that the category is hidden.
     */
    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'visible' => false,
        ]);
    }
}
