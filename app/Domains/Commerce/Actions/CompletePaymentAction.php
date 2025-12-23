<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Actions;

use App\Domains\Auth\Models\User;
use App\Domains\Commerce\Events\OrderPaid;
use App\Domains\Commerce\Models\Order;
use App\Domains\Courses\Actions\EnrollUserAction;
use App\Domains\Courses\Exceptions\AlreadyEnrolledException;
use App\Domains\Courses\Models\Course;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

/**
 * Action to complete payment and provision enrollments
 * Following Single Responsibility Principle
 */
class CompletePaymentAction
{
    public function __construct(
        private readonly EnrollUserAction $enrollUserAction
    ) {
    }

    /**
     * Complete payment for an order and provision enrollments
     *
     * @param Order $order The order to complete
     * @param string $transactionId The payment gateway transaction ID
     * @return Order The updated order
     */
    public function handle(Order $order, string $transactionId): Order
    {
        return DB::transaction(function () use ($order, $transactionId) {
            $order->update([
                'status' => OrderStatus::Paid,
                'transaction_id' => $transactionId,
            ]);

            $this->provisionEnrollments($order);

            $order = $order->fresh();

            // Dispatch event after payment is completed
            OrderPaid::dispatch($order);

            return $order;
        });
    }

    /**
     * Provision enrollments for all courses in the order
     */
    private function provisionEnrollments(Order $order): void
    {
        if (!$order->user_id) {
            return;
        }

        $user = User::find($order->user_id);
        if (!$user) {
            return;
        }

        foreach ($order->items as $item) {
            if ($item->orderable_type === Course::class) {
                /** @var Course $course */
                $course = Course::find($item->orderable_id);
                
                if ($course) {
                    try {
                        $this->enrollUserAction->handle($user, $course);
                    } catch (AlreadyEnrolledException $e) {
                        // User might already be enrolled, skip
                        continue;
                    }
                }
            }
        }
    }
}

