<div class="space-y-6">
    <div>
        <h3 class="text-lg font-bold mb-4">نظرات ({{ $comments->count() }})</h3>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Comments List --}}
        <div class="space-y-6">
            @forelse($comments as $comment)
                <div class="border-b border-gray-200 pb-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-gray-600 font-semibold">
                                    {{ mb_substr($comment->user->name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold">{{ $comment->user->name }}</span>
                                <span class="text-sm text-gray-500">{{ $comment->created_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <p class="text-gray-700 whitespace-pre-line">{{ $comment->body }}</p>

                            {{-- Replies --}}
                            @if($comment->replies->isNotEmpty())
                                <div class="mt-4 pr-8 space-y-4 border-r-2 border-gray-200">
                                    @foreach($comment->replies as $reply)
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-gray-600 text-sm font-semibold">
                                                        {{ mb_substr($reply->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="font-semibold text-sm">{{ $reply->user->name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $reply->created_at->format('Y/m/d H:i') }}</span>
                                                </div>
                                                <p class="text-gray-700 text-sm whitespace-pre-line">{{ $reply->body }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">هنوز نظری ثبت نشده است.</p>
            @endforelse
        </div>
    </div>

    {{-- Comment Form --}}
    @auth
        <div class="mt-8">
            <h4 class="text-lg font-semibold mb-4">نظر خود را بنویسید</h4>
            <form wire:submit="submit">
                <div class="mb-4">
                    <textarea 
                        wire:model="body"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="نظر خود را اینجا بنویسید..."
                    ></textarea>
                    @error('body') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
                <button 
                    type="submit"
                    class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition"
                >
                    ارسال نظر
                </button>
            </form>
        </div>
    @else
        <div class="mt-8 p-4 bg-gray-100 rounded-lg text-center">
            <p class="text-gray-700 mb-2">برای ارسال نظر باید وارد شوید.</p>
            <a href="#" data-action="open-login" class="text-primary-600 hover:underline">ورود / ثبت‌نام</a>
        </div>
    @endauth
</div>
