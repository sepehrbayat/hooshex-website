@props([
    'image' => 'figma-images/images/image-23.png',
    'alt' => 'بک‌گراند دوره گادمود هوشکس',
])

<x-ui.section id="banner" background="transparent">
    <div class="relative rounded-[32px] px-6 py-10 md:px-8 md:py-12 text-center shadow-2xl shadow-primary-600/40 overflow-hidden" style="width: 100%; max-width: 1216px; min-height: 200px; md:min-height: 300px; lg:min-height: 504px;">
        <img 
            src="{{ asset($image) }}" 
            alt="{{ $alt }}" 
            class="absolute inset-0 h-full w-full object-cover"
            style="object-position: center; image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges; image-rendering: high-quality;"
            loading="eager"
            fetchpriority="high"
        />
    </div>
</x-ui.section>

