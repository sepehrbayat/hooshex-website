<?php

namespace Database\Factories;

use App\Domains\Auth\Models\User;
use App\Domains\Courses\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Courses\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();
        $specialties = [
            'هوش مصنوعی',
            'یادگیری ماشین',
            'برنامه‌نویسی',
            'طراحی وب',
            'علم داده',
            'امنیت سایبری',
        ];
        $selectedSpecialties = $this->faker->randomElements($specialties, 2);

        return [
            'user_id' => User::factory(),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 10000),
            'bio' => $this->faker->paragraphs(3, true),
            'specialty' => implode('، ', $selectedSpecialties),
            'social_links' => [
                'linkedin' => $this->faker->url(),
                'twitter' => $this->faker->url(),
            ],
            'published_at' => now()->subDays($this->faker->numberBetween(1, 365)),
        ];
    }
}
