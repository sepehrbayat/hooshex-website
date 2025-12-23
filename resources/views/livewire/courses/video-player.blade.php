<div class="space-y-4">
    <div class="aspect-video bg-black rounded-lg flex items-center justify-center text-white">
        <span>Video placeholder for: {{ $lesson->title }}</span>
    </div>
    @if($lesson->is_free_preview)
        <div class="text-sm text-green-600">پیش‌نمایش رایگان</div>
    @endif
</div>

