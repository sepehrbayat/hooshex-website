<x-layouts.app :page="$page">
    <main class="flex-1">
        <article class="max-w-4xl mx-auto px-4 py-8">
            <header class="mb-8">
                <h1 class="text-3xl font-bold mb-4">{{ $page->title }}</h1>
                @if($page->excerpt)
                    <p class="text-lg text-gray-600">{{ $page->excerpt }}</p>
                @endif
            </header>

            @if($page->content_blocks)
                <div class="prose max-w-none">
                    @foreach($page->content_blocks as $blockType => $blockData)
                        @php
                            $blockData = is_string($blockData) ? json_decode($blockData, true) : $blockData;
                        @endphp
                        
                        @switch($blockType)
                            @case('hero')
                                <x-page-blocks.hero :data="$blockData" />
                                @break
                            
                            @case('features')
                                <x-page-blocks.features :data="$blockData" />
                                @break
                            
                            @case('content')
                                <x-page-blocks.content :data="$blockData" />
                                @break
                            
                            @default
                                <div class="my-8 p-4 bg-gray-100 rounded">
                                    <p class="text-sm text-gray-600">Block type "{{ $blockType }}" not implemented</p>
                                    <pre class="mt-2 text-xs">{{ json_encode($blockData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                        @endswitch
                    @endforeach
                </div>
            @else
                <div class="prose max-w-none">
                    <p>No content blocks defined for this page.</p>
                </div>
            @endif
        </article>
    </main>
</x-layouts.app>
