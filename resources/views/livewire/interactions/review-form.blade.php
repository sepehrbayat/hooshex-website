<div class="space-y-6">
    {{-- Reviews Summary --}}
    @if($totalReviews > 0)
        <div class="bg-gray-50 p-6 rounded-lg">
            <div class="flex items-center gap-4 mb-4">
                <div class="text-4xl font-bold">{{ $averageRating }}</div>
                <div>
                    <div class="flex items-center gap-1 mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600">بر اساس {{ $totalReviews }} نقد</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Reviews List --}}
    <div>
        <h3 class="text-lg font-bold mb-4">نقد و بررسی‌ها ({{ $totalReviews }})</h3>
        <div class="space-y-6">
            @forelse($reviews as $review)
                <div class="border-b border-gray-200 pb-4">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-gray-600 font-semibold">
                                    {{ mb_substr($review->user->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <div class="font-semibold">{{ $review->user->name }}</div>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500">{{ $review->created_at->format('Y/m/d') }}</span>
                    </div>
                    @if($review->title)
                        <h4 class="font-semibold mb-2">{{ $review->title }}</h4>
                    @endif
                    @if($review->body)
                        <p class="text-gray-700 whitespace-pre-line">{{ $review->body }}</p>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">هنوز نقدی ثبت نشده است.</p>
            @endforelse
        </div>
    </div>

    {{-- Review Form --}}
    @auth
        @if(!$hasReviewed)
            <div class="mt-8 border-t pt-8">
                <h4 class="text-lg font-semibold mb-4">نقد خود را بنویسید</h4>
                <form wire:submit="submit">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">امتیاز</label>
                        <div class="flex gap-2">
                            @for($i = 5; $i >= 1; $i--)
                                <button 
                                    type="button"
                                    wire:click="$set('rating', {{ $i }})"
                                    class="focus:outline-none"
                                >
                                    <svg class="w-8 h-8 transition {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }}" 
                                         fill="currentColor" 
                                         viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        @error('rating') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">عنوان (اختیاری)</label>
                        <input 
                            type="text"
                            wire:model="title"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="عنوان نقد"
                        >
                        @error('title') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">متن نقد (اختیاری)</label>
                        <textarea 
                            wire:model="body"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="نقد خود را اینجا بنویسید..."
                        ></textarea>
                        @error('body') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    <button 
                        type="submit"
                        class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition"
                    >
                        ارسال نقد
                    </button>
                </form>
            </div>
        @else
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-blue-700">شما قبلاً برای این مورد نقد ارسال کرده‌اید.</p>
            </div>
        @endif
    @else
        <div class="mt-8 p-4 bg-gray-100 rounded-lg text-center">
            <p class="text-gray-700 mb-2">برای ارسال نقد باید وارد شوید.</p>
            <a href="#" data-action="open-login" class="text-primary-600 hover:underline">ورود / ثبت‌نام</a>
        </div>
    @endauth
</div>
