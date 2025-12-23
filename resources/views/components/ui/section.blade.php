@props([
    'id' => null,
    'background' => 'transparent', // transparent, surface
    'container' => 'default', // default, wide
])

@php
    $backgroundClass = match($background) {
        'surface' => 'section-base-surface',
        default => 'section-base',
    };
    
    $containerClass = match($container) {
        'wide' => 'section-container-wide',
        default => 'section-container',
    };
@endphp

<section
    @if($id) id="{{ $id }}" @endif
    {{ $attributes->merge([
        'class' => $backgroundClass,
        'dir' => 'rtl',
    ]) }}
>
    <div class="{{ $containerClass }}">
        {{ $slot }}
    </div>
</section>
