<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Listeners;

use App\Domains\Commerce\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Send order confirmation email when order is created
 */
class SendOrderConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        // TODO: Implement email sending logic
        // Mail::to($event->order->user)->send(new OrderConfirmationMail($event->order));
    }
}

