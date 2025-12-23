@props([
    'variant' => 'primary', // primary, secondary, outline
    'size' => 'md', // sm, md, lg
    'pill' => false,
    'icon' => false,
    'type' => 'button',
])

@php
    $baseClasses = 'btn';
    $variantClasses = match($variant) {
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'outline' => 'btn-secondary',
        default => 'btn-primary',
    };
    $sizeClasses = match($size) {
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
        default => '',
    };
    $pillClasses = $pill ? 'btn-pill' : '';
    $iconClasses = $icon ? ($size === 'sm' ? 'btn-icon-sm' : ($size === 'lg' ? 'btn-icon-lg' : 'btn-icon')) : '';
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => trim("{$baseClasses} {$variantClasses} {$sizeClasses} {$pillClasses} {$iconClasses}"),
    ]) }}
>
    {{ $slot }}
</button>

