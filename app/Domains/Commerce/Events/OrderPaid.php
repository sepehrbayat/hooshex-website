<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Events;

use App\Domains\Commerce\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when an order payment is completed
 * This is a critical event that triggers enrollment provisioning
 */
class OrderPaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Order $order
    ) {
    }
}

