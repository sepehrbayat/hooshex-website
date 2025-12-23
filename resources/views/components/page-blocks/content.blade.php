@props(['data'])

@php
    $content = $data['content'] ?? $data['html'] ?? null;
    $title = $data['title'] ?? null;
    $class = $data['class'] ?? 'prose max-w-none';
@endphp

<section class="py-8 lg:py-12 bg-surface">
    <div class="container mx-auto px-4">
        @if($title)
            <h2 class="text-3xl font-bold text-text-primary mb-6">
                {{ $title }}
            </h2>
        @endif

        @if($content)
            <div class="{{ $class }}">
                {!! $content !!}
            </div>
        @else
            <div class="prose max-w-none">
                <p class="text-text-secondary">No content provided for this block.</p>
            </div>
        @endif
    </div>
</section>
