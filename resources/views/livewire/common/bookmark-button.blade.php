<div dir="rtl">
    <button 
        wire:click="toggle"
        wire:loading.attr="disabled"
        class="group flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200
            {{ $isBookmarked 
                ? 'bg-primary-100 text-primary-600' 
                : 'bg-primary-50 text-text-secondary hover:bg-primary-100' 
            }}"
        title="{{ $isBookmarked ? 'حذف از علاقه‌مندی‌ها' : 'افزودن به علاقه‌مندی‌ها' }}"
        style="font-family: 'Vazirmatn', sans-serif;"
    >
        {{-- Loading State --}}
        <span wire:loading wire:target="toggle" class="inline-block">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>

        {{-- Bookmark Icon --}}
        <span wire:loading.remove wire:target="toggle">
            @if($isBookmarked)
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                </svg>
            @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-5-7 5V5z"/>
                </svg>
            @endif
        </span>

        {{-- Count --}}
        @if($bookmarkCount > 0)
            <span class="text-sm font-medium">{{ number_format($bookmarkCount) }}</span>
        @endif
    </button>
</div>
