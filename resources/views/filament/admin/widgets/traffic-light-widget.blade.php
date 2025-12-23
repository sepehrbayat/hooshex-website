<x-filament::section>
    <div class="flex items-center space-x-4 space-x-reverse">
        <div class="w-3 h-3 rounded-full {{ $analysis['grade'] === 'green' ? 'bg-green-500' : ($analysis['grade'] === 'yellow' ? 'bg-yellow-400' : 'bg-red-500') }}"></div>
        <div>
            <div class="font-semibold">SEO Score: {{ $analysis['score'] }}</div>
            <div class="text-xs text-gray-500">
                هدینگ‌ها: {{ $analysis['details']['headings'] }} |
                پاراگراف‌ها: {{ $analysis['details']['paragraphs'] }} |
                کاراکترها: {{ $analysis['details']['length'] }}
            </div>
        </div>
    </div>
</x-filament::section>

