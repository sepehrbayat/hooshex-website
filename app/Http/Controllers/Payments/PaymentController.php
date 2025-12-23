<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payments;

use App\Domains\Commerce\Actions\CompletePaymentAction;
use App\Domains\Commerce\Actions\CreateOrderFromCartAction;
use App\Domains\Commerce\Models\Order;
use App\Domains\Commerce\Services\Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class PaymentController extends Controller
{
    public function __construct(
        private readonly Cart $cart,
        private readonly CreateOrderFromCartAction $createOrderAction,
        private readonly CompletePaymentAction $completePaymentAction
    ) {
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 401);

        try {
            $order = $this->createOrderAction->handle($user, 'zarinpal');
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $invoice = (new Invoice())->amount($order->total_amount);
        $callback = route('payment.callback', ['order' => $order->id]);

        return Payment::callbackUrl($callback)
            ->purchase($invoice, function ($driver, $transactionId) use ($order) {
                $order->update(['gateway_ref_id' => $transactionId]);
            })->pay()->render();
    }

    public function callback(Request $request)
    {
        $order = Order::findOrFail($request->query('order'));

        try {
            $receipt = Payment::amount($order->total_amount)
                ->transactionId($order->gateway_ref_id)
                ->verify();

            $this->completePaymentAction->handle($order, $receipt->getReferenceId());

            return response('پرداخت موفق', 200);
        } catch (\Exception $e) {
            $order->update(['status' => \App\Enums\OrderStatus::Failed]);
            return response('خطا در پرداخت', 400);
        }
    }

}

