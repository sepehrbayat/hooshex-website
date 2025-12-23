@props(['data'])

@php
    $title = $data['title'] ?? 'Features';
    $subtitle = $data['subtitle'] ?? null;
    $features = $data['items'] ?? [];
@endphp

<section class="py-16 lg:py-24 bg-surface">
    <div class="container mx-auto px-4">
        @if($title || $subtitle)
            <div class="text-center mb-12">
                @if($title)
                    <h2 class="text-3xl md:text-4xl font-bold text-text-primary mb-4">
                        {{ $title }}
                    </h2>
                @endif
                @if($subtitle)
                    <p class="text-lg text-text-secondary max-w-2xl mx-auto">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        @endif

        @if(count($features) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($features as $feature)
                    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                        @if(isset($feature['icon']))
                            <div class="w-12 h-12 mb-4 text-primary-600">
                                {!! $feature['icon'] !!}
                            </div>
                        @endif

                        @if(isset($feature['title']))
                            <h3 class="text-xl font-semibold text-text-primary mb-2">
                                {{ $feature['title'] }}
                            </h3>
                        @endif

                        @if(isset($feature['description']))
                            <p class="text-text-secondary">
                                {{ $feature['description'] }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
