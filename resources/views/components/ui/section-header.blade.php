@props([
    'title' => '',
    'rawTitle' => false, // If true, title is rendered as HTML
    'subtitle' => null,
    'action' => null,
    'actionLabel' => 'مشاهده همه',
    'align' => 'right', // right, center, left
])

@php
    $alignClasses = match($align) {
        'center' => 'items-center text-center',
        'left' => 'items-start text-left',
        default => 'items-end text-right',
    };
@endphp

<div {{ $attributes->merge(['class' => "flex flex-col gap-4 md:flex-row md:items-center md:justify-between w-full {$alignClasses}"]) }}>
    <div class="flex flex-col gap-2 w-full md:w-auto">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold leading-tight text-text-primary">
            @if($rawTitle)
                {!! $title !!}
            @else
                {{ $title }}
            @endif
        </h2>
        @if($subtitle)
            <p class="text-base md:text-lg text-text-secondary">
                {{ $subtitle }}
            </p>
        @endif
    </div>
    
    @if($action)
        <a
            href="{{ $action }}"
            wire:navigate
            class="inline-flex items-center gap-2 text-base font-normal text-text-primary transition hover:text-primary-600"
        >
            <span>{{ $actionLabel }}</span>
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="scale-x-[-1]">
                <path d="M4.75 2.3335L9.33333 7.00016L4.75 11.6668" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    @endif
</div>
