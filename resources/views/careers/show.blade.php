@php
    $page = $career; // For SEO injection in layout
@endphp

<x-layouts.app>
    <main class="flex-1">
        <article class="max-w-7xl mx-auto px-4 py-8" dir="rtl">
            {{-- Breadcrumb Section --}}
            <nav class="mb-6" aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-600">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-primary-600">خانه</a>
                    </li>
                    <li>/</li>
                    <li>
                        <span>فرصت‌های شغلی</span>
                    </li>
                    <li>/</li>
                    <li class="text-gray-900 font-medium">{{ $career->title }}</li>
                </ol>
            </nav>

            {{-- Hero Header --}}
            <header class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $career->title }}</h1>
                
                {{-- Badges Row --}}
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    @if($career->location)
                        <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-lg">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">{{ $career->location }}</span>
                        </div>
                    @endif

                    @if($career->contract_type)
                        <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-lg">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">{{ $career->contract_type->label() }}</span>
                        </div>
                    @endif

                    @if($career->work_type)
                        <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-lg">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">{{ $career->work_type->label() }}</span>
                        </div>
                    @endif

                    @if($career->department)
                        <div class="flex items-center gap-2 px-4 py-2 bg-primary-50 rounded-lg">
                            <span class="text-sm font-medium text-primary-700">{{ $career->department }}</span>
                        </div>
                    @endif
                </div>

                {{-- CTA Button --}}
                @if($career->application_link)
                    <a href="{{ $career->application_link }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors">
                        <span>ارسال رزومه</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @endif
            </header>

            {{-- Main Layout Grid --}}
            <div class="grid grid-cols-12 gap-8">
                {{-- Content Column (Right in RTL) --}}
                <div class="col-span-12 lg:col-span-8">
                    {{-- About Role Section --}}
                    @if($career->description)
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">درباره این موقعیت</h2>
                            <div class="prose prose-lg prose-slate max-w-none prose-headings:scroll-mt-24 prose-a:text-primary-600 hover:prose-a:text-primary-700">
                                {!! $career->description !!}
                            </div>
                        </section>
                    @endif

                    {{-- Responsibilities Section --}}
                    @if($career->responsibilities && count($career->responsibilities) > 0)
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">مسئولیت‌ها</h2>
                            <ul class="space-y-3 list-disc list-inside text-gray-700">
                                @foreach($career->responsibilities as $responsibility)
                                    <li class="text-lg">{{ $responsibility['item'] ?? $responsibility }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    {{-- Requirements Section --}}
                    @if($career->requirements && count($career->requirements) > 0)
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">نیازمندی‌ها</h2>
                            <ul class="space-y-3 list-disc list-inside text-gray-700">
                                @foreach($career->requirements as $requirement)
                                    <li class="text-lg">{{ $requirement['item'] ?? $requirement }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    {{-- Benefits Section --}}
                    @if($career->benefits && count($career->benefits) > 0)
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">مزایا</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($career->benefits as $benefit)
                                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                        <svg class="w-6 h-6 text-primary-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-gray-700">{{ $benefit['item'] ?? $benefit }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                {{-- Sidebar Column (Left in RTL, Sticky) --}}
                <aside class="col-span-12 lg:col-span-4">
                    <div class="sticky top-24 space-y-6">
                        {{-- Job Summary Box --}}
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">خلاصه موقعیت</h3>
                            <div class="space-y-4">
                                @if($career->department)
                                    <div>
                                        <span class="text-sm text-gray-600">دپارتمان:</span>
                                        <p class="text-base font-medium text-gray-900">{{ $career->department }}</p>
                                    </div>
                                @endif

                                @if($career->experience_level)
                                    <div>
                                        <span class="text-sm text-gray-600">سطح تجربه:</span>
                                        <p class="text-base font-medium text-gray-900">{{ $career->experience_level }}</p>
                                    </div>
                                @endif

                                @if($career->salary_range)
                                    <div>
                                        <span class="text-sm text-gray-600">حقوق:</span>
                                        <p class="text-base font-medium text-gray-900">{{ $career->salary_range }}</p>
                                    </div>
                                @endif

                                @if($career->published_at)
                                    <div>
                                        <span class="text-sm text-gray-600">تاریخ انتشار:</span>
                                        <p class="text-base font-medium text-gray-900">
                                            @if(function_exists('verta'))
                                                {{ verta($career->published_at)->format('j F Y') }}
                                            @else
                                                {{ $career->published_at->format('Y/m/d') }}
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                @if($career->expires_at)
                                    <div>
                                        <span class="text-sm text-gray-600">مهلت درخواست:</span>
                                        <p class="text-base font-medium text-gray-900">
                                            @if(function_exists('verta'))
                                                {{ verta($career->expires_at)->format('j F Y') }}
                                            @else
                                                {{ $career->expires_at->format('Y/m/d') }}
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- Apply Button in Sidebar --}}
                            @if($career->application_link)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <a href="{{ $career->application_link }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="block w-full text-center px-4 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors">
                                        ارسال رزومه
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Share Buttons (Optional) --}}
                        <div class="bg-white rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">اشتراک‌گذاری</h3>
                            <div class="flex gap-3">
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('careers.show', $career->slug)) }}&text={{ urlencode($career->title) }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                                    </svg>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('careers.show', $career->slug)) }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                </a>
                                <button onclick="navigator.share({title: '{{ $career->title }}', url: '{{ route('careers.show', $career->slug) }}'})" 
                                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </article>
    </main>
</x-layouts.app>

