@props(['data'])

@php
    $title = $data['title'] ?? 'Hero Title';
    $subtitle = $data['subtitle'] ?? null;
    $description = $data['description'] ?? null;
    $image = $data['image'] ?? null;
    $ctaText = $data['cta_text'] ?? null;
    $ctaLink = $data['cta_link'] ?? null;
    $background = $data['background'] ?? null;
@endphp

<section class="relative bg-surface py-16 lg:py-24 {{ $background ? "bg-[$background]" : '' }}">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            @if($title)
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-text-primary mb-6">
                    {{ $title }}
                </h1>
            @endif

            @if($subtitle)
                <h2 class="text-2xl md:text-3xl text-primary-600 mb-4">
                    {{ $subtitle }}
                </h2>
            @endif

            @if($description)
                <p class="text-lg text-text-secondary mb-8 max-w-2xl mx-auto">
                    {{ $description }}
                </p>
            @endif

            @if($ctaText && $ctaLink)
                <div class="flex justify-center gap-4">
                    <a href="{{ $ctaLink }}" class="btn btn-primary btn-lg">
                        {{ $ctaText }}
                    </a>
                </div>
            @endif

            @if($image)
                <div class="mt-12">
                    <img src="{{ $image }}" alt="{{ $title }}" class="mx-auto rounded-lg shadow-lg max-w-full">
                </div>
            @endif
        </div>
    </div>
</section>
