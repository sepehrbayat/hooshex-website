<div
    x-data="{ show: @entangle('show') }"
    x-show="show"
    x-on:keydown.escape.window="@this.call('close')"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    dir="rtl"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-text-primary/50 backdrop-blur-sm"
        @if($closeable) @click="$wire.close()" @endif
    ></div>

    {{-- Modal Container --}}
    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            {{-- Modal Panel --}}
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-xl bg-white text-right shadow-xl transition-all sm:my-8 w-full {{ $this->getMaxWidthClass() }}"
                style="font-family: 'Vazirmatn', sans-serif;"
            >
                {{-- Close Button --}}
                @if($closeable)
                <button
                    @click="$wire.close()"
                    class="absolute top-4 left-4 p-2 rounded-lg text-text-muted hover:text-text-primary hover:bg-primary-50 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif

                {{-- Modal Content (slot) --}}
                <div class="p-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
