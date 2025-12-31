<div 
    x-data="{ 
        toasts: @entangle('toasts'),
        removeToast(id) {
            $wire.removeToast(id);
        }
    }"
    @remove-toast.window="setTimeout(() => removeToast($event.detail.id), $event.detail.delay)"
    class="fixed bottom-4 left-4 z-50 flex flex-col gap-2"
    dir="rtl"
>
    <template x-for="(toast, id) in toasts" :key="id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg min-w-[280px] max-w-md"
            style="font-family: 'Vazirmatn', sans-serif;"
            :class="{
                'bg-primary-600 text-white': toast.type === 'success',
                'bg-accent-500 text-white': toast.type === 'error',
                'bg-accent-400 text-white': toast.type === 'warning',
                'bg-primary-400 text-white': toast.type === 'info',
            }"
        >
            {{-- Icon --}}
            <div class="flex-shrink-0">
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
            </div>

            {{-- Message --}}
            <span x-text="toast.message" class="flex-1 text-sm font-medium"></span>

            {{-- Close Button --}}
            <button 
                @click="removeToast(id)"
                class="flex-shrink-0 opacity-70 hover:opacity-100 transition-opacity"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
