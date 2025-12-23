<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Listeners;

use App\Domains\Commerce\Events\OrderPaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Send payment confirmation email when order is paid
 */
class SendPaymentConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        // TODO: Implement email sending logic
        // Mail::to($event->order->user)->send(new PaymentConfirmationMail($event->order));
    }
}

