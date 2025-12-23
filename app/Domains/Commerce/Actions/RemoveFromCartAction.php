<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Actions;

use App\Domains\Commerce\Services\Cart;

/**
 * Action to remove an item from cart
 * Following Single Responsibility Principle
 */
class RemoveFromCartAction
{
    public function __construct(
        private readonly Cart $cart
    ) {
    }

    /**
     * Remove an item from cart by key
     *
     * @param string $key The cart item key (e.g., 'course_1')
     * @return void
     */
    public function handle(string $key): void
    {
        $this->cart->remove($key);
    }
}

