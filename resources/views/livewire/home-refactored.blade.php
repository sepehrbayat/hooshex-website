@php
    $testimonials = [
        ['name' => 'نسترن', 'text' => 'دوره خیلی مفیدی بود و مطالب رو به شکل کاربردی توضیح می‌داد. پروژه‌های عملی کمک کرد تجربه واقعی پیدا کنم. در کل بنظرم ارزش وقت و هزینه‌اش رو داشت.'],
        ['name' => 'محمد', 'text' => 'قبل از این دوره هیچ تجربه‌ای از وردپرس نداشتم، ولی قدم‌به‌قدم یاد گرفتم سایت رو از صفر طراحی کنم. اما توضیحات و تمرین‌ها کمک کرد خوب وسریع پیش برم.'],
        ['name' => 'سحر', 'text' => 'دوره خیلی مفیدی بود و مطالب رو به شکل کاربردی توضیح می‌داد. پروژه‌های عملی کمک کرد تجربه واقعی پیدا کنم. در کل بنظرم ارزش وقت و هزینه‌اش رو داشت.'],
    ];

    $blogs = [
        ['title' => 'آموزش هوش مصنوعی برای کسب‌وکارها', 'tag' => 'جدید'],
        ['title' => 'چطور از هوش مصنوعی پول بسازیم؟', 'tag' => 'محبوب'],
        ['title' => 'راهنمای ابزارهای هوشکس', 'tag' => 'جدید'],
    ];
@endphp

<div class="bg-surface overflow-x-hidden">
    {{-- Hero Section --}}
    <x-home.hero />

    {{-- Features Section --}}
    <x-home.features />

    {{-- Career Paths Section --}}
    <x-home.career-paths />

    {{-- Super App Section --}}
    <x-home.super-app />

    {{-- Popular Courses Section --}}
    <x-home.popular-courses />

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

