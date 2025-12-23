@props([
    'course' => null,
    'badgeStyles' => [],
])

@php
    // Handle both array and object (Course model)
    $isModel = is_object($course);
    $title = $isModel ? $course->title : ($course['title'] ?? '');
    // Support both 'image' and 'thumbnail_path' for arrays
    $thumbnail = $isModel ? $course->thumbnail_path : ($course['thumbnail_path'] ?? $course['image'] ?? '');
    $badge = $isModel ? ($course->is_featured ? 'محبوب' : 'جدید') : ($course['badge'] ?? '');
    $price = $isModel ? ($course->sale_price ?? $course->price ?? 0) : ($course['price'] ?? 0);
    $oldPrice = $isModel ? ($course->sale_price ? $course->price : null) : ($course['oldPrice'] ?? null);
    $instructor = $isModel ? $course->teacher : ($course['instructor'] ?? null);
    $slug = $isModel ? $course->slug : ($course['slug'] ?? '');
    
    // Badge color mapping - Pixel Perfect from Figma
    $badgeColor = match($badge) {
        'محبوب' => 'bg-[#EB55C8]', // Secondary/500
        'جدید' => 'bg-[#775FEE]', // Primary/400
        '20 درصد تخیف', '۲۰ درصد تخیف' => 'bg-[#EB55C8]', // Secondary/500
        default => 'bg-[#EB55C8]',
    };
    
    // Format price
    $formattedPrice = number_format((float)$price, 0) . ' تومان';
    $formattedOldPrice = $oldPrice ? number_format((float)$oldPrice, 0) . ' تومان' : null;
    
    // Course link - fallback to ai-tools.index if no specific course route exists
    $courseLink = route('ai-tools.index');
@endphp

{{-- Course Card - Pixel Perfect from Figma --}}
<article class="relative flex min-h-[411px] w-full max-w-[292px] flex-col overflow-visible rounded-[8px] bg-[rgba(119,95,238,0.1)] mx-auto" dir="rtl" style="height: auto; min-height: 411px;">
    {{-- Badge and Icon - Pixel Perfect --}}
    <div class="absolute right-4 top-4 z-10 flex items-center gap-2" style="width: 252px; height: 24px;">
        {{-- Category Icon (hidden in some cards) --}}
        <div class="h-6 w-6 flex-shrink-0"></div>
        
        {{-- Badge - Pixel Perfect from Figma --}}
        @if($badge)
        <span class="inline-flex h-[23.62px] items-center justify-center rounded-[8px] {{ $badgeColor }} px-[13px] py-2 text-[10px] font-normal leading-[15px] text-[#fcf1fb]" style="font-family: 'Vazirmatn', sans-serif; font-weight: 400;">
            {{ $badge }}
        </span>
        @endif
    </div>

    {{-- Course Image - Pixel Perfect --}}
    <div class="flex items-center justify-center px-4 pt-16 pb-4">
        @if($thumbnail)
        <img
            src="{{ asset($thumbnail) }}"
            alt="تصویر دوره {{ $title }}"
            loading="lazy"
            class="h-[158.22px] w-[161.13px] object-contain"
        />
        @else
        <div class="h-[158.22px] w-[161.13px] bg-primary-50 rounded-lg flex items-center justify-center">
            <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        @endif
    </div>

    {{-- Content - Pixel Perfect --}}
    <div class="flex flex-1 flex-col gap-[27px] px-5 pb-6 w-full" style="max-width: 258px;">
        {{-- Title and Instructor - Pixel Perfect --}}
        <div class="flex flex-col gap-4">
            {{-- Title - Pixel Perfect --}}
            <h3 class="text-[16px] font-black leading-[24px] text-[#1a1a1a]" style="font-family: 'Vazirmatn', sans-serif; font-weight: 900;">{{ $title }}</h3>

            {{-- Instructor Info - Pixel Perfect --}}
            @if($instructor)
                <div class="flex items-center gap-2" dir="rtl">
                    <div class="flex h-[30px] w-[30px] shrink-0 items-center justify-center rounded-[5px] bg-[#eb55c8]">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M8 2C6.34315 2 5 3.34315 5 5C5 6.65685 6.34315 8 8 8C9.65685 8 11 6.65685 11 5C11 3.34315 9.65685 2 8 2Z" fill="white"/>
                            <path d="M3 13C3 10.7909 4.79086 9 7 9H9C11.2091 9 13 10.7909 13 13V14H3V13Z" fill="white"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[12px] font-medium leading-[18px] text-[#1a1a1a]" style="font-family: 'Vazirmatn', sans-serif; font-weight: 500;">{{ $isModel ? $instructor->name : ($instructor['name'] ?? '') }}</span>
                        <span class="text-[10px] font-normal leading-[15px] text-[#4d4d4d]" style="font-family: 'Vazirmatn', sans-serif; font-weight: 400;">{{ $isModel ? 'مدرس' : ($instructor['role'] ?? '') }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Price and Button - Pixel Perfect from Figma --}}
        {{-- Frame 1890167552: width: 252px, height: 32px --}}
        <div class="mt-auto flex flex-col gap-2 w-full pt-2 pb-1" style="max-width: 252px;">
            {{-- Old Price (if exists) - Pixel Perfect --}}
            @if($formattedOldPrice)
                <p class="text-[10px] font-medium leading-[15px] text-[#4d4d4d] text-right mb-0" style="font-family: 'Vazirmatn', sans-serif; font-weight: 500; text-decoration: line-through;">{{ $formattedOldPrice }}</p>
            @endif
            
            {{-- Final Price and Button Container - Frame 1890167552: width: 252px, height: 32px --}}
            <div class="flex items-center justify-between w-full" style="width: 252px; height: 32px; min-height: 32px;" dir="rtl">
                {{-- Price Text - Pixel Perfect: width: 130px, height: 24px, font-size: 16px, line-height: 24px --}}
                <span class="text-[16px] font-medium leading-[24px] text-[#1a1a1a] flex items-center flex-shrink-0" style="font-family: 'Vazirmatn', sans-serif; font-weight: 500; font-feature-settings: 'ss01' on, 'ss02' on, 'ss03' on, 'ss04' on; font-variation-settings: 'DOTS' 7; min-width: 130px; height: 24px; text-align: right;">{{ $formattedPrice }}</span>
                
                {{-- Button - Pixel Perfect: width: 112px, height: 32px, padding: 10px 24px, gap: 8px --}}
                <a
                    href="{{ $courseLink }}"
                    wire:navigate
                    class="inline-flex items-center justify-center rounded-[16px] border-2 border-[#eb55c8] text-[12px] font-medium leading-[18px] text-[#eb55c8] transition hover:opacity-90 whitespace-nowrap flex-shrink-0"
                    style="font-family: 'Vazirmatn', sans-serif; font-weight: 500; font-feature-settings: 'ss01' on, 'ss02' on, 'ss03' on, 'ss04' on; font-variation-settings: 'DOTS' 7; width: 112px; min-width: 112px; height: 32px; min-height: 32px; padding: 10px 24px; gap: 8px; text-align: right;"
                >
                    مشاهده دوره
                </a>
            </div>
        </div>
    </div>
</article>
