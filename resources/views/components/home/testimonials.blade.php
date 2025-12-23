@props([
    'testimonials' => [],
])

<x-ui.section id="testimonials" background="transparent">
    <x-ui.section-header 
        title='<span class="text-text-primary">درباره ما چی </span><span class="text-accent-500">میگن؟</span>'
        :raw-title="true"
        align="right"
    />
    
    {{-- Testimonials Cards Container - Swiper Slider --}}
    <div class="relative w-full">
        {{-- Fade Gradient Overlays --}}
        <div class="absolute left-0 top-0 bottom-0 w-16 bg-gradient-to-r from-surface to-transparent z-10 pointer-events-none"></div>
        <div class="absolute right-0 top-0 bottom-0 w-16 bg-gradient-to-l from-surface to-transparent z-10 pointer-events-none"></div>
        
        <div class="swiper testimonials-swiper" dir="rtl">
            <div class="swiper-wrapper">
                @foreach ($testimonials as $index => $item)
                    <div class="swiper-slide">
                        <div class="flex justify-center px-2">
                            <x-home.testimonial-card :item="$item" :index="$index" />
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Navigation Buttons --}}
            <button class="testimonials-swiper-button-prev absolute left-0 top-1/2 -translate-y-1/2 z-20 p-3 rounded-full bg-white/80 backdrop-blur-sm text-primary-600 hover:bg-white hover:shadow-lg transition shadow-md" aria-label="نظر قبلی">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button class="testimonials-swiper-button-next absolute right-0 top-1/2 -translate-y-1/2 z-20 p-3 rounded-full bg-white/80 backdrop-blur-sm text-primary-600 hover:bg-white hover:shadow-lg transition shadow-md" aria-label="نظر بعدی">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            
            {{-- Pagination --}}
            <div class="flex items-center justify-center mt-6">
                <div class="testimonials-swiper-pagination"></div>
            </div>
        </div>
    </div>
</x-ui.section>
