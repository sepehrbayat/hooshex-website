<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Events;

use App\Domains\Commerce\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a new order is created
 */
class OrderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Order $order
    ) {
    }
}

