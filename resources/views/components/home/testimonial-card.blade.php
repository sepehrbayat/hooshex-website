@props([
    'item' => [],
    'index' => 0,
])

<div class="card rounded-lg bg-white shadow-md p-6 w-full max-w-[371px] flex flex-col">
    {{-- Stars Rating --}}
    <div class="flex gap-2 mb-4">
        @for($i = 0; $i < 5; $i++)
            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
        @endfor
    </div>
    
    {{-- Testimonial Text --}}
    <p class="text-text-secondary text-sm leading-relaxed mb-4 flex-1">
        {{ $item['text'] ?? '' }}
    </p>
    
    {{-- Author Name --}}
    <div class="text-text-primary font-semibold">
        {{ $item['name'] ?? '' }}
    </div>
</div>
