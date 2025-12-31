@php
    $order = $order ?? null;
@endphp

@if($order && $order->licenses->count() > 0)
    <div class="space-y-4">
        @foreach($order->licenses as $license)
            <div class="p-4 border rounded-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-semibold text-lg">{{ $license->course->title }}</h4>
                        <div class="mt-2 space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">کلید لایسنس:</span>
                                <code class="px-2 py-1 bg-gray-100 rounded text-sm">{{ $license->license_key }}</code>
                                <button 
                                    type="button"
                                    onclick="navigator.clipboard.writeText('{{ $license->license_key }}')"
                                    class="text-primary-600 hover:text-primary-700"
                                    title="کپی">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                            @if($license->expires_at)
                                <div>
                                    <span class="font-medium">تاریخ انقضا:</span>
                                    <span>{{ $license->expires_at->format('Y/m/d H:i') }}</span>
                                </div>
                            @else
                                <div>
                                    <span class="font-medium">دسترسی:</span>
                                    <span class="text-green-600">مادام‌العمر</span>
                                </div>
                            @endif
                            <div>
                                <span class="font-medium">وضعیت:</span>
                                @if($license->is_active && !$license->isExpired())
                                    <span class="text-green-600">فعال</span>
                                @elseif($license->isExpired())
                                    <span class="text-red-600">منقضی شده</span>
                                @else
                                    <span class="text-gray-600">غیرفعال</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a 
                        href="{{ route('courses.show', $license->course->slug) }}" 
                        target="_blank"
                        class="text-primary-600 hover:text-primary-700">
                        مشاهده دوره →
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-gray-600">این سفارش هیچ لایسنسی ندارد.</p>
@endif

