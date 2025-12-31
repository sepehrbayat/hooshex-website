<?php

namespace Database\Factories;

use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Teacher;
use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Courses\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 10000),
            'short_description' => $this->faker->paragraph(),
            'description' => $this->faker->paragraphs(5, true),
            'price' => $this->faker->randomElement([0, 50000, 100000, 200000, 500000]),
            'sale_price' => null,
            'duration' => $this->faker->numberBetween(60, 6000),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'status' => CourseStatus::Published,
            'is_featured' => $this->faker->boolean(20),
            'is_certificate_available' => $this->faker->boolean(70),
            'published_at' => now()->subDays($this->faker->numberBetween(1, 365)),
        ];
    }

    /**
     * Indicate that the course is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseStatus::Draft,
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the course is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the course is free.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => 0,
            'discounted_price' => null,
        ]);
    }
}
