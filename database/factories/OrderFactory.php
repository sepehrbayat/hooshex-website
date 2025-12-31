<?php

namespace Database\Factories;

use App\Domains\Auth\Models\User;
use App\Domains\Commerce\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Commerce\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => OrderStatus::Pending,
            'total_amount' => $this->faker->randomElement([50000, 100000, 200000, 500000]),
            'gateway' => 'zarinpal',
            'gateway_ref_id' => null,
            'transaction_id' => null,
        ];
    }

    /**
     * Indicate that the order is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Paid,
            'transaction_id' => 'TX' . $this->faker->unique()->numberBetween(100000, 999999),
        ]);
    }

    /**
     * Indicate that the order has failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Failed,
        ]);
    }
}
