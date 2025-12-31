@props([
    'blog' => [],
    'index' => 0,
    'image' => 'figma-images/images/image-19.png',
])

@php
    // Calculate image index from blogImageNodes array
    $blogImageNodes = [
        'figma-images/images/image-19.png',
        'figma-images/images/image-20.png',
        'figma-images/images/image-21.png',
        'figma-images/images/image-22.png',
    ];
    $imagePath = $image ?? ($blogImageNodes[$index % count($blogImageNodes)] ?? 'figma-images/images/image-19.png');
@endphp

<div class="card rounded-lg overflow-hidden bg-white shadow-md w-full max-w-[292px] h-[308px] flex flex-col">
    {{-- Blog Image --}}
    <div class="relative w-full h-[180px] overflow-hidden bg-primary-50">
        <img 
            src="{{ asset($image) }}" 
            alt="{{ $blog['title'] ?? '' }}" 
            class="w-full h-full object-cover"
            loading="lazy"
        />
        @if(isset($blog['tag']))
            <div class="absolute top-3 left-3 px-2 py-1 bg-accent-500 text-white text-xs font-medium rounded">
                {{ $blog['tag'] }}
            </div>
        @endif
    </div>
    
    {{-- Blog Content --}}
    <div class="flex-1 flex flex-col p-4">
        <h3 class="text-lg font-bold text-text-primary mb-2 line-clamp-2">
            {{ $blog['title'] ?? '' }}
        </h3>
        <p class="text-sm text-text-secondary mb-4 line-clamp-2 flex-1">
            {{ $blog['excerpt'] ?? '' }}
        </p>
        <a 
            href="{{ $blog['url'] ?? '#' }}" 
            class="text-primary-600 text-sm font-medium hover:text-primary-800 transition"
        >
            مطالعه بیشتر →
        </a>
    </div>
</div>
