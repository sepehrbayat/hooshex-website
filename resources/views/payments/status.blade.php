@extends('components.layouts.app')

@section('title', 'وضعیت سفارش')

@section('content')
<div class="bg-surface min-h-screen py-12 md:py-16 lg:py-20" dir="rtl">
    <div class="section-container max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-black text-text-primary" style="font-family: 'Vazirmatn', sans-serif;">
                    جزئیات سفارش
                </h1>
                <span class="px-3 py-1 text-sm font-semibold rounded-full
                    @switch($order->status->value)
                        @case('paid')
                            bg-primary-50 text-primary-600
                            @break
                        @case('pending')
                            bg-accent-400/20 text-accent-500
                            @break
                        @case('failed')
                            bg-accent-400/20 text-accent-500
                            @break
                        @default
                            bg-primary-50 text-text-muted
                    @endswitch
                " style="font-family: 'Vazirmatn', sans-serif;">
                    {{ $order->status->label() }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-text-muted" style="font-family: 'Vazirmatn', sans-serif;">شماره سفارش:</span>
                    <span class="font-semibold text-text-primary mr-2" style="font-family: 'Vazirmatn', sans-serif;">#{{ $order->id }}</span>
                </div>
                <div>
                    <span class="text-text-muted" style="font-family: 'Vazirmatn', sans-serif;">تاریخ ثبت:</span>
                    <span class="font-semibold text-text-primary mr-2" style="font-family: 'Vazirmatn', sans-serif;">
                        @if (class_exists(\Morilog\Jalali\Jalalian::class))
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($order->created_at)->format('Y/m/d H:i') }}
                        @else
                            {{ $order->created_at->format('Y/m/d H:i') }}
                        @endif
                    </span>
                </div>
                @if($order->transaction_id)
                <div class="col-span-2">
                    <span class="text-text-muted" style="font-family: 'Vazirmatn', sans-serif;">کد پیگیری:</span>
                    <span class="font-mono text-text-primary mr-2">{{ $order->transaction_id }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Order Items --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="font-bold text-text-primary mb-4" style="font-family: 'Vazirmatn', sans-serif;">آیتم‌های سفارش</h2>
            
            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center gap-4 p-4 bg-primary-50 rounded-lg">
                    @if($item->orderable && $item->orderable->thumbnail_path)
                    <img src="{{ asset($item->orderable->thumbnail_path) }}" 
                         alt="{{ $item->orderable->title ?? '' }}"
                         class="w-16 h-16 object-cover rounded-lg">
                    @else
                    <div class="w-16 h-16 bg-surface rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    @endif
                    
                    <div class="flex-1">
                        <h3 class="font-semibold text-text-primary" style="font-family: 'Vazirmatn', sans-serif;">
                            {{ $item->orderable->title ?? 'محصول حذف شده' }}
                        </h3>
                        <p class="text-sm text-text-muted" style="font-family: 'Vazirmatn', sans-serif;">
                            تعداد: {{ $item->quantity }}
                        </p>
                    </div>
                    
                    <div class="text-left">
                        <span class="font-bold text-text-primary" style="font-family: 'Vazirmatn', sans-serif;">
                            {{ number_format($item->price * $item->quantity) }} تومان
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Total --}}
            <div class="border-t border-primary-50 mt-6 pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-text-primary" style="font-family: 'Vazirmatn', sans-serif;">مجموع:</span>
                    <span class="text-xl font-bold text-primary-600" style="font-family: 'Vazirmatn', sans-serif;">
                        {{ number_format($order->total_amount) }} تومان
                    </span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            @if(in_array($order->status->value, ['pending', 'failed']))
            <a href="{{ route('payment.retry', $order) }}" 
               class="flex-1 py-3 px-4 bg-primary-600 hover:bg-primary-500 text-white rounded-btn font-semibold text-center transition-colors"
               style="font-family: 'Vazirmatn', sans-serif;">
                پرداخت مجدد
            </a>
            @endif
            <a href="{{ route('dashboard') }}" 
               class="flex-1 py-3 px-4 bg-primary-50 hover:bg-accent-400 text-text-primary rounded-btn font-semibold text-center transition-colors"
               style="font-family: 'Vazirmatn', sans-serif;">
                داشبورد
            </a>
        </div>
    </div>
</div>
@endsection
