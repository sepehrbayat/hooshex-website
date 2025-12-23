<div class="grid grid-cols-1 gap-6 md:grid-cols-4">
    <aside class="md:col-span-1 space-y-6">
        <div>
            <h3 class="font-semibold mb-2">قیمت</h3>
            @foreach ($pricingOptions as $option)
                <label class="flex items-center space-x-2 space-x-reverse mb-2">
                    <input type="checkbox" wire:model.live="pricing" value="{{ strtolower($option) }}"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span>{{ $option }}</span>
                </label>
            @endforeach
        </div>

        <div>
            <h3 class="font-semibold mb-2">دسته‌بندی‌ها</h3>
            @foreach ($categoryOptions as $category)
                <label class="flex items-center space-x-2 space-x-reverse mb-2">
                    <input type="checkbox" wire:model.live="categories" value="{{ $category->slug }}"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span>{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
    </aside>

    <section class="md:col-span-3">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($tools as $tool)
                <article class="border rounded-lg p-4 shadow-sm bg-white">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-semibold text-lg">{{ $tool->name }}</h2>
                        @if ($tool->is_verified)
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">تایید شده</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 line-clamp-3 mb-3">{{ $tool->short_description }}</p>
                    <div class="flex items-center justify-between text-sm text-gray-700">
                        <span>قیمت: {{ $tool->pricing_type ?? 'نامشخص' }}</span>
                        <span>امتیاز: {{ number_format($tool->rating, 1) }}</span>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $tools->links() }}
        </div>
    </section>
</div>

