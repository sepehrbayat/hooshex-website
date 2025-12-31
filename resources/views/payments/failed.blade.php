@extends('components.layouts.app')

@section('title', 'خطا در پرداخت')

@section('content')
<div class="bg-surface min-h-screen py-12 md:py-16 lg:py-20" dir="rtl">
    <div class="section-container flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center">
            {{-- Error Icon --}}
            <div class="mb-6">
                <div class="mx-auto w-20 h-20 bg-accent-400/20 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl font-black text-text-primary mb-2" style="font-family: 'Vazirmatn', sans-serif;">
                پرداخت ناموفق
            </h1>
            
            <p class="text-text-muted mb-6" style="font-family: 'Vazirmatn', sans-serif;">
                {{ $message ?? 'متأسفانه پرداخت شما با خطا مواجه شد.' }}
            </p>

            {{-- Order Details --}}
            <div class="bg-primary-50 rounded-lg p-4 mb-6 text-right">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-text-muted text-sm" style="font-family: 'Vazirmatn', sans-serif;">شماره سفارش:</span>
                    <span class="font-semibold text-text-primary" style="font-family: 'Vazirmatn', sans-serif;">{{ $order->id }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-text-muted text-sm" style="font-family: 'Vazirmatn', sans-serif;">مبلغ:</span>
                    <span class="font-bold text-text-primary" style="font-family: 'Vazirmatn', sans-serif;">
                        {{ number_format($order->total_amount) }} تومان
                    </span>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="bg-accent-400/10 border border-accent-400/30 rounded-lg p-4 mb-6 text-right">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-accent-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-text-secondary" style="font-family: 'Vazirmatn', sans-serif;">
                        در صورت کسر وجه از حساب شما، مبلغ پرداختی تا ۷۲ ساعت آینده به حسابتان برگشت داده خواهد شد.
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col gap-3">
                <a href="{{ route('payment.retry', $order) }}" 
                   class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-500 text-white rounded-btn font-semibold transition-colors"
                   style="font-family: 'Vazirmatn', sans-serif;">
                    تلاش مجدد
                </a>
                <a href="{{ route('cart.index') }}" 
                   class="w-full py-3 px-4 bg-primary-50 hover:bg-accent-400 text-text-primary rounded-btn font-semibold transition-colors"
                   style="font-family: 'Vazirmatn', sans-serif;">
                    بازگشت به سبد خرید
                </a>
                <a href="{{ route('home') }}" 
                   class="text-primary-600 hover:text-primary-500 text-sm transition-colors"
                   style="font-family: 'Vazirmatn', sans-serif;">
                    بازگشت به صفحه اصلی
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
