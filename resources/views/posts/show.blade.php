@php
    $page = $post; // For SEO injection in layout
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
                        <span>بلاگ</span>
                    </li>
                    @if($post->primaryCategory)
                        <li>/</li>
                        <li>
                            <a href="#" class="hover:text-primary-600">{{ $post->primaryCategory->name }}</a>
                        </li>
                    @endif
                    <li>/</li>
                    <li class="text-gray-900 font-medium">{{ $post->title }}</li>
                </ol>
            </nav>

            {{-- Breadcrumb Schema.org JSON-LD --}}
            <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "BreadcrumbList",
                "itemListElement": [
                    {
                        "@type": "ListItem",
                        "position": 1,
                        "name": "خانه",
                        "item": "{{ route('home') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "بلاگ",
                        "item": "#"
                    }@if($post->primaryCategory),
                    {
                        "@type": "ListItem",
                        "position": 3,
                        "name": "{{ $post->primaryCategory->name }}",
                        "item": "#"
                    }@endif,
                    {
                        "@type": "ListItem",
                        "position": {{ $post->primaryCategory ? 4 : 3 }},
                        "name": "{{ $post->title }}",
                        "item": "{{ route('posts.show', $post->slug) }}"
                    }
                ]
            }
            </script>

            {{-- Hero Header --}}
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
                
                {{-- Meta Row --}}
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-6">
                    @if($post->author)
                        <div class="flex items-center gap-2">
                            @if($post->author->avatar_path)
                                <img src="{{ asset($post->author->avatar_path) }}" 
                                     alt="{{ $post->author->name }}" 
                                     class="rounded-full w-10 h-10 object-cover">
                            @else
                                <div class="rounded-full w-10 h-10 bg-gray-300 flex items-center justify-center text-gray-600 font-medium">
                                    {{ mb_substr($post->author->name, 0, 1, 'UTF-8') }}
                                </div>
                            @endif
                            <a href="#" class="hover:text-primary-600 font-medium">
                                {{ $post->author->name }}
                            </a>
                        </div>
                    @endif
                    
                    @if($post->updated_at)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            آخرین بروزرسانی: 
                            @if(function_exists('verta'))
                                {{ verta($post->updated_at)->format('j F Y') }}
                            @else
                                {{ $post->updated_at->format('Y/m/d') }}
                            @endif
                        </span>
                    @endif
                    
                    @if($post->reading_time)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-primary-50 text-primary-700 rounded-full text-xs font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $post->reading_time }} دقیقه مطالعه
                        </span>
                    @endif
                </div>
            </header>

            {{-- Main Layout Grid --}}
            <div class="grid grid-cols-12 gap-8">
                {{-- Content Column --}}
                <div class="col-span-12 lg:col-span-8">
                    @if($post->thumbnail)
                        <img src="{{ $post->thumbnail->url }}" 
                             alt="{{ $post->title }}" 
                             class="w-full h-64 object-cover rounded-xl mb-8">
                    @elseif($post->thumbnail_path)
                        <img src="{{ asset($post->thumbnail_path) }}" 
                             alt="{{ $post->title }}" 
                             class="w-full h-64 object-cover rounded-xl mb-8">
                    @endif

                    @if($post->excerpt)
                        <p class="text-lg text-gray-600 mb-6">{{ $post->excerpt }}</p>
                    @endif

                    <div class="prose prose-lg prose-slate max-w-none prose-headings:scroll-mt-24 prose-img:rounded-xl prose-a:text-primary-600 hover:prose-a:text-primary-700">
                        {!! $parsedContent ?? $post->content !!}
                    </div>
                </div>

                {{-- Sidebar Column --}}
                <aside class="col-span-12 lg:col-span-4">
                    <div class="sticky top-24 space-y-6">
                        {{-- TOC Widget --}}
                        @if(!empty($toc))
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-bold text-gray-900 mb-4">در این صفحه</h2>
                                <nav class="space-y-2">
                                    @foreach($toc as $item)
                                        <a href="#{{ $item['id'] }}" 
                                           class="block text-gray-600 hover:text-primary-600 transition-colors {{ $item['level'] === 3 ? 'mr-4' : '' }}">
                                            {{ $item['text'] }}
                                        </a>
                                    @endforeach
                                </nav>
                            </div>
                        @endif

                        {{-- Related Posts --}}
                        @php
                            $relatedPosts = \App\Domains\Blog\Models\Post::where('status', \App\Enums\PostStatus::Published)
                                ->whereNotNull('published_at')
                                ->where('published_at', '<=', now())
                                ->where('id', '!=', $post->id)
                                ->when($post->primary_category_id, function ($query) use ($post) {
                                    return $query->where('primary_category_id', $post->primary_category_id);
                                })
                                ->with('thumbnail')
                                ->latest('published_at')
                                ->limit(3)
                                ->get();
                        @endphp

                        @if($relatedPosts->isNotEmpty())
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-bold text-gray-900 mb-4">مطالب مرتبط</h2>
                                <div class="space-y-4">
                                    @foreach($relatedPosts as $relatedPost)
                                        <a href="{{ route('posts.show', $relatedPost->slug) }}" 
                                           class="block group">
                                            @if($relatedPost->thumbnail)
                                                <img src="{{ $relatedPost->thumbnail->url }}" 
                                                     alt="{{ $relatedPost->title }}" 
                                                     class="w-full h-32 object-cover rounded-lg mb-2 group-hover:opacity-90 transition-opacity">
                                            @endif
                                            <h3 class="text-sm font-medium text-gray-900 group-hover:text-primary-600 line-clamp-2">
                                                {{ $relatedPost->title }}
                                            </h3>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>

            {{-- Footer Area --}}
            <footer class="mt-12 pt-8 border-t space-y-8">
                {{-- Tags Section --}}
                @if($post->tags && $post->tags->isNotEmpty())
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">برچسب‌ها</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                                <a href="#" 
                                   class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-primary-100 hover:text-primary-700 transition-colors">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Author Bio Box --}}
                @if($post->author && ($post->author->bio || $post->author->social_links))
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-start gap-4">
                            @if($post->author->avatar_path)
                                <img src="{{ asset($post->author->avatar_path) }}" 
                                     alt="{{ $post->author->name }}" 
                                     class="rounded-full w-16 h-16 object-cover flex-shrink-0">
                            @else
                                <div class="rounded-full w-16 h-16 bg-gray-300 flex items-center justify-center text-gray-600 font-bold text-xl flex-shrink-0">
                                    {{ mb_substr($post->author->name, 0, 1, 'UTF-8') }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $post->author->name }}</h3>
                                @if($post->author->bio)
                                    <p class="text-gray-600 mb-3">{{ $post->author->bio }}</p>
                                @endif
                                @if($post->author->social_links && is_array($post->author->social_links))
                                    <div class="flex gap-3">
                                        @foreach($post->author->social_links as $platform => $url)
                                            <a href="{{ $url }}" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="text-gray-400 hover:text-primary-600 transition-colors">
                                                <span class="sr-only">{{ $platform }}</span>
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Comments Section --}}
                <section class="mt-12 border-t pt-8">
                    <livewire:interactions.comment-section :model="$post" />
                </section>
            </footer>
        </article>
    </main>
</x-layouts.app>
