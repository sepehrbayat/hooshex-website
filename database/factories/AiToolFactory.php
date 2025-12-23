<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\AiTools\Models\AiTool;
use App\Enums\PricingType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AiTool>
 */
class AiToolFactory extends Factory
{
    protected $model = AiTool::class;

    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name.'-'.$this->faker->unique()->numberBetween(1, 9999)),
            'pricing_type' => PricingType::Free,
            'short_description' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'rating' => 4.5,
        ];
    }
}

