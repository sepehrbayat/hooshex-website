<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payments;

use App\Domains\Commerce\Actions\CompletePaymentAction;
use App\Domains\Commerce\Actions\CreateOrderFromCartAction;
use App\Domains\Commerce\Exceptions\CartEmptyException;
use App\Domains\Commerce\Exceptions\PaymentFailedException;
use App\Domains\Commerce\Models\Order;
use App\Domains\Commerce\Services\Cart;
use App\Enums\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
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

    /**
     * Initiate checkout and redirect to payment gateway
     */
    public function checkout(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'لطفاً ابتدا وارد حساب کاربری خود شوید.');
        }

        try {
            $order = $this->createOrderAction->handle($user, 'zarinpal');
        } catch (CartEmptyException $e) {
            return response()->json([
                'success' => false,
                'message' => 'سبد خرید شما خالی است.',
            ], 422);
        } catch (\Exception $e) {
            Log::error('Payment checkout error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در ایجاد سفارش. لطفاً دوباره تلاش کنید.',
            ], 500);
        }

        $invoice = (new Invoice())->amount($order->total_amount);
        $callback = route('payment.callback', ['order' => $order->id]);

        try {
            return Payment::callbackUrl($callback)
                ->purchase($invoice, function ($driver, $transactionId) use ($order) {
                    $order->update(['gateway_ref_id' => $transactionId]);
                })->pay()->render();
        } catch (\Exception $e) {
            Log::error('Payment gateway error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            $order->update(['status' => OrderStatus::Failed]);
            
            return redirect()->route('cart.index')
                ->with('error', 'خطا در اتصال به درگاه پرداخت. لطفاً دوباره تلاش کنید.');
        }
    }

    /**
     * Handle payment gateway callback
     */
    public function callback(Request $request): View|RedirectResponse
    {
        $orderId = $request->query('order');
        
        if (!$orderId) {
            return redirect()->route('home')
                ->with('error', 'سفارش یافت نشد.');
        }

        $order = Order::find($orderId);
        
        if (!$order) {
            return redirect()->route('home')
                ->with('error', 'سفارش یافت نشد.');
        }

        // Check if order is already processed
        if ($order->status === OrderStatus::Paid) {
            return redirect()->route('dashboard')
                ->with('success', 'این سفارش قبلاً پرداخت شده است.');
        }

        try {
            $receipt = Payment::amount($order->total_amount)
                ->transactionId($order->gateway_ref_id)
                ->verify();

            $this->completePaymentAction->handle($order, $receipt->getReferenceId());

            Log::info('Payment successful', [
                'order_id' => $order->id,
                'transaction_id' => $receipt->getReferenceId(),
                'amount' => $order->total_amount,
            ]);

            return view('payments.success', [
                'order' => $order->fresh(),
                'transactionId' => $receipt->getReferenceId(),
            ]);

        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            $order->update(['status' => OrderStatus::Failed]);

            return view('payments.failed', [
                'order' => $order,
                'message' => 'پرداخت ناموفق بود. در صورت کسر وجه، مبلغ تا ۷۲ ساعت آینده به حساب شما برگشت داده خواهد شد.',
            ]);
        }
    }

    /**
     * Show order status/history for user
     */
    public function status(Order $order): View|RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user || $order->user_id !== $user->id) {
            return redirect()->route('login');
        }

        return view('payments.status', [
            'order' => $order->load('items.orderable'),
        ]);
    }

    /**
     * Retry payment for a failed order
     */
    public function retry(Order $order): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user || $order->user_id !== $user->id) {
            return redirect()->route('login');
        }

        if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Failed])) {
            return response()->json([
                'success' => false,
                'message' => 'این سفارش قابل پرداخت مجدد نیست.',
            ], 422);
        }

        // Reset order status
        $order->update([
            'status' => OrderStatus::Pending,
            'gateway_ref_id' => null,
        ]);

        $invoice = (new Invoice())->amount($order->total_amount);
        $callback = route('payment.callback', ['order' => $order->id]);

        try {
            return Payment::callbackUrl($callback)
                ->purchase($invoice, function ($driver, $transactionId) use ($order) {
                    $order->update(['gateway_ref_id' => $transactionId]);
                })->pay()->render();
        } catch (\Exception $e) {
            Log::error('Payment retry error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('payment.status', $order)
                ->with('error', 'خطا در اتصال به درگاه پرداخت.');
        }
    }
}

