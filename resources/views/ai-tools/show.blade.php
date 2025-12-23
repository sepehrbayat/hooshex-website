@php
    // Pass aiTool to layout for SEO
    $page = $aiTool;
@endphp

<x-layouts.app>
    <main class="flex-1">
        <article class="max-w-7xl mx-auto px-4 py-8">
            {{-- Hero Section --}}
            <header class="mb-8">
                <div class="flex items-start gap-6 mb-6">
                    @if($aiTool->logo)
                        <img src="{{ $aiTool->logo->url }}" alt="{{ $aiTool->name }}" class="w-24 h-24 object-contain rounded-lg">
                    @elseif($aiTool->logo_path)
                        <img src="{{ asset($aiTool->logo_path) }}" alt="{{ $aiTool->name }}" class="w-24 h-24 object-contain rounded-lg">
                    @endif
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold">{{ $aiTool->name }}</h1>
                            @if($aiTool->is_verified)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    تأیید شده
                                </span>
                            @endif
                        </div>
                        @if($aiTool->short_description)
                            <p class="text-lg text-gray-600 mb-4">{{ $aiTool->short_description }}</p>
                        @endif
                    </div>
                </div>

                {{-- Primary CTA --}}
                <div class="flex gap-4 mb-6">
                    @if($aiTool->affiliate_url || $aiTool->website_url)
                        <a href="{{ route('click.track', $aiTool->slug) }}" target="_blank" class="btn btn-primary">
                            بازدید از سایت
                        </a>
                    @endif
                    @if($aiTool->deal_url)
                        <a href="{{ $aiTool->deal_url }}" target="_blank" class="btn btn-secondary">
                            پیشنهاد ویژه
                        </a>
                    @endif
                    @if($aiTool->demo_url)
                        <a href="{{ $aiTool->demo_url }}" target="_blank" class="btn btn-outline">
                            دمو
                        </a>
                    @endif
                </div>

                {{-- Meta Bar --}}
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    @if($aiTool->pricing_type)
                        <span class="badge badge-primary">
                            {{ match($aiTool->pricing_type->value) {
                                'free' => 'رایگان',
                                'freemium' => 'فرییمیوم',
                                'paid' => 'پولی',
                                'free_trial' => 'آزمایشی رایگان',
                                'contact' => 'تماس بگیرید',
                                default => $aiTool->pricing_type->value,
                            } }}
                        </span>
                    @endif
                    @if($aiTool->categories->isNotEmpty())
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500">دسته‌بندی‌ها:</span>
                            @foreach($aiTool->categories as $category)
                                <span class="badge badge-secondary">{{ $category->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($aiTool->rating)
                        <div class="flex items-center gap-1">
                            <span>⭐</span>
                            <span>{{ number_format($aiTool->rating, 1) }}</span>
                        </div>
                    @endif
                </div>
            </header>

            {{-- Gallery Grid --}}
            @if($aiTool->gallery_media->isNotEmpty())
                <section class="mb-12">
                    <h2 class="text-2xl font-bold mb-4">گالری تصاویر</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($aiTool->gallery_media as $media)
                            <div class="rounded-lg overflow-hidden shadow-md">
                                <img src="{{ $media->url }}" alt="{{ $aiTool->name }}" class="w-full h-48 object-cover">
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Content Section --}}
            @if($aiTool->content)
                <section class="mb-12">
                    <div class="prose max-w-none">
                        {!! $aiTool->content !!}
                    </div>
                </section>
            @endif

            {{-- Pros and Cons --}}
            @if(($aiTool->pros && count($aiTool->pros) > 0) || ($aiTool->cons && count($aiTool->cons) > 0))
                <section class="mb-12 grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($aiTool->pros && count($aiTool->pros) > 0)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <h3 class="text-xl font-bold mb-4 text-green-800">نقاط قوت</h3>
                            <ul class="space-y-2">
                                @foreach($aiTool->pros as $pro)
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $pro }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($aiTool->cons && count($aiTool->cons) > 0)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                            <h3 class="text-xl font-bold mb-4 text-red-800">نقاط ضعف</h3>
                            <ul class="space-y-2">
                                @foreach($aiTool->cons as $con)
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $con }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </section>
            @endif

            {{-- Features Grid --}}
            @if($aiTool->features && is_array($aiTool->features) && count($aiTool->features) > 0)
                <section class="mb-12">
                    <h2 class="text-2xl font-bold mb-6">ویژگی‌ها</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($aiTool->features as $feature)
                            <div class="border rounded-lg p-4">
                                @if(is_array($feature))
                                    @if(isset($feature['icon']))
                                        <div class="mb-2 text-2xl">{{ $feature['icon'] }}</div>
                                    @endif
                                    <h4 class="font-semibold mb-1">{{ $feature['title'] ?? $feature['name'] ?? '' }}</h4>
                                    @if(isset($feature['description']))
                                        <p class="text-sm text-gray-600">{{ $feature['description'] }}</p>
                                    @endif
                                @else
                                    <p>{{ $feature }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Reviews Section --}}
            <section class="mt-12 border-t pt-8">
                <livewire:interactions.review-form :model="$aiTool" />
            </section>
        </article>
    </main>
</x-layouts.app>
