<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Actions;

use App\Domains\Auth\Models\User;
use App\Domains\Commerce\Events\OrderCreated;
use App\Domains\Commerce\Exceptions\CartEmptyException;
use App\Domains\Commerce\Models\Order;
use App\Domains\Commerce\Models\OrderItem;
use App\Domains\Commerce\Services\Cart;
use App\Domains\Courses\Models\Course;
use App\Enums\OrderStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Action to create an order from cart items
 * Following Single Responsibility Principle
 */
class CreateOrderFromCartAction
{
    public function __construct(
        private readonly Cart $cart
    ) {
    }

    /**
     * Create an order from current cart items
     *
     * @param User $user The user creating the order
     * @param string $gateway Payment gateway (default: 'zarinpal')
     * @return Order The created order
     * @throws CartEmptyException If cart is empty
     */
    public function handle(User $user, string $gateway = 'zarinpal'): Order
    {
        $items = $this->cart->items();
        
        if ($items->isEmpty()) {
            throw new CartEmptyException();
        }

        return DB::transaction(function () use ($user, $items, $gateway) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => OrderStatus::Pending,
                'total_amount' => $items->sum(fn ($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1)),
                'gateway' => $gateway,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'orderable_type' => $item['type'] ?? Course::class,
                    'orderable_id' => $item['id'],
                    'price' => $item['price'] ?? 0,
                    'quantity' => $item['quantity'] ?? 1,
                ]);
            }

            // Dispatch event
            OrderCreated::dispatch($order);

            return $order;
        });
    }
}

