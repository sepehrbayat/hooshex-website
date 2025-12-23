@php
    // Pass course to layout for SEO
    $page = $course;
@endphp

<x-layouts.app>
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 py-8">
            {{-- Hero Section with Video --}}
            <header class="mb-8">
                <div class="mb-6">
                    @if($course->intro_video_provider === 'aparat' && $course->intro_video_id)
                        {{-- Video Embed --}}
                        <div class="video-container rounded-xl overflow-hidden shadow-2xl">
                            {!! $course->embed_html !!}
                        </div>
                    @elseif($course->thumbnail)
                        {{-- Fallback to Thumbnail --}}
                        <img src="{{ $course->thumbnail->url }}" alt="{{ $course->title }}" class="w-full h-64 object-cover rounded-xl shadow-2xl">
                    @elseif($course->thumbnail_path)
                        {{-- Legacy Thumbnail Path --}}
                        <img src="{{ asset($course->thumbnail_path) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover rounded-xl shadow-2xl">
                    @endif
                </div>
                
                <h1 class="text-3xl font-bold mb-4">{{ $course->title }}</h1>
                @if($course->short_description)
                    <p class="text-lg text-gray-600 mb-4">{{ $course->short_description }}</p>
                @endif
            </header>

            {{-- Two Column Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Content --}}
                <div class="lg:col-span-2">
                    @if($course->description)
                        <section class="mb-12">
                            <div class="prose max-w-none">
                                {!! $course->description !!}
                            </div>
                        </section>
                    @endif

                    {{-- Curriculum Accordion --}}
                    @if($course->chapters->isNotEmpty())
                        <section class="mb-12">
                            <h2 class="text-2xl font-bold mb-6">سرفصل‌های دوره</h2>
                            <div class="space-y-4">
                                @foreach($course->chapters as $chapter)
                                    <div class="border rounded-lg overflow-hidden">
                                        <div class="bg-gray-50 px-6 py-4 border-b">
                                            <h3 class="font-semibold text-lg">{{ $chapter->title }}</h3>
                                        </div>
                                        @if($chapter->lessons->isNotEmpty())
                                            <div class="divide-y">
                                                @foreach($chapter->lessons as $lesson)
                                                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                                                        <div class="flex items-center gap-3 flex-1">
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="text-gray-600">{{ $lesson->title }}</span>
                                                                    @if($lesson->is_free_preview || $lesson->is_free)
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                            پیش‌نمایش رایگان
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                @if($lesson->duration)
                                                                    <span class="text-sm text-gray-500">{{ $lesson->duration }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Reviews Section --}}
                    <section class="mt-12 border-t pt-8">
                        <livewire:interactions.review-form :model="$course" />
                    </section>
                </div>

                {{-- Sticky Sidebar --}}
                <aside class="lg:col-span-1">
                    <div class="sticky top-8">
                        <div class="bg-white border rounded-lg shadow-lg p-6">
                            {{-- Price Display --}}
                            <div class="mb-6">
                                @if($course->sale_price && $course->sale_price < $course->price)
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-2xl font-bold text-primary-600">{{ number_format($course->sale_price) }} تومان</span>
                                        <span class="text-lg text-gray-400 line-through">{{ number_format($course->price) }}</span>
                                    </div>
                                @else
                                    <div class="text-2xl font-bold text-primary-600 mb-2">
                                        @if($course->price > 0)
                                            {{ number_format($course->price) }} تومان
                                        @else
                                            رایگان
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Guarantee Text --}}
                            @if($course->guarantee_text)
                                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-sm text-green-800">{{ $course->guarantee_text }}</p>
                                </div>
                            @endif

                            {{-- Enroll Button --}}
                            <div class="mb-6">
                                <livewire:commerce.add-to-cart-button :product="$course" />
                            </div>

                            {{-- Meta List --}}
                            <div class="space-y-3 text-sm border-t pt-6">
                                @if($course->duration)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">مدت زمان:</span>
                                        <span class="font-medium">{{ $course->duration }}</span>
                                    </div>
                                @endif
                                @if($course->level)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">سطح:</span>
                                        <span class="font-medium">
                                            {{ match($course->level->value) {
                                                'beginner' => 'مبتدی',
                                                'intermediate' => 'متوسط',
                                                'advanced' => 'پیشرفته',
                                                default => $course->level->value,
                                            } }}
                                        </span>
                                    </div>
                                @endif
                                @if($course->updated_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">آخرین بروزرسانی:</span>
                                        <span class="font-medium">{{ $course->updated_at->format('Y/m/d') }}</span>
                                    </div>
                                @endif
                                @if($course->teacher)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">مدرس:</span>
                                        <span class="font-medium">{{ $course->teacher->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>
</x-layouts.app>
