@extends('components.layouts.app')

@section('title', 'پرداخت موفق')

@section('content')
<div class="bg-surface min-h-screen py-12 md:py-16 lg:py-20" dir="rtl">
    <div class="section-container flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center">
            {{-- Success Icon --}}
            <div class="mb-6">
                <div class="mx-auto w-20 h-20 bg-primary-50 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl font-black text-text-primary mb-2" style="font-family: 'Vazirmatn', sans-serif;">
                پرداخت موفقیت‌آمیز بود
            </h1>
            
            <p class="text-text-muted mb-6" style="font-family: 'Vazirmatn', sans-serif;">
                از خرید شما سپاسگزاریم. سفارش شما با موفقیت ثبت شد.
            </p>

            {{-- Order Details --}}
            <div class="bg-primary-50 rounded-lg p-4 mb-6 text-right">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-text-muted text-sm" style="font-family: 'Vazirmatn', sans-serif;">شماره سفارش:</span>
                    <span class="font-semibold text-text-primary" style="font-family: 'Vazirmatn', sans-serif;">{{ $order->id }}</span>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-text-muted text-sm" style="font-family: 'Vazirmatn', sans-serif;">کد پیگیری:</span>
                    <span class="font-mono text-sm text-text-primary">{{ $transactionId }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-text-muted text-sm" style="font-family: 'Vazirmatn', sans-serif;">مبلغ پرداختی:</span>
                    <span class="font-bold text-primary-600" style="font-family: 'Vazirmatn', sans-serif;">
                        {{ number_format($order->total_amount) }} تومان
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col gap-3">
                <a href="{{ route('dashboard') }}" 
                   class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-500 text-white rounded-btn font-semibold transition-colors"
                   style="font-family: 'Vazirmatn', sans-serif;">
                    مشاهده دوره‌های من
                </a>
                <a href="{{ route('home') }}" 
                   class="w-full py-3 px-4 bg-primary-50 hover:bg-accent-400 text-text-primary rounded-btn font-semibold transition-colors"
                   style="font-family: 'Vazirmatn', sans-serif;">
                    بازگشت به صفحه اصلی
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
