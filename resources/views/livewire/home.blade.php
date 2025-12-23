<div class="bg-surface overflow-x-hidden">
    {{-- Hero Section --}}
    <x-home.hero />

    {{-- Features Section --}}
    <x-home.features :features="$features" />

    {{-- Career Paths Section --}}
    <x-home.career-paths />

    {{-- Super App Section --}}
    <x-home.super-app />

    {{-- Popular Courses Section --}}
    <x-home.popular-courses :courses="$courses" />

    {{-- AI Bot Generator Section --}}
    <x-home.ai-bot />

    {{-- Instagram Section --}}
    <x-home.instagram />

    {{-- Testimonials Section --}}
    <x-home.testimonials :testimonials="$testimonials" />

    {{-- Blog Section --}}
    <x-home.blog :blogs="$blogs" />

    {{-- Banner Section --}}
    <x-home.banner />
</div>
