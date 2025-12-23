<div>
    @if(session('cart_message'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('cart_message') }}
        </div>
    @endif

    @if(session('cart_error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('cart_error') }}
        </div>
    @endif

    <button
        wire:click="addToCart"
        wire:loading.attr="disabled"
        wire:target="addToCart"
        class="w-full btn btn-primary {{ $isLoading ? 'opacity-50 cursor-not-allowed' : '' }}"
        @if($isLoading) disabled @endif
    >
        <span wire:loading.remove wire:target="addToCart">
            ثبت‌نام در دوره
        </span>
        <span wire:loading wire:target="addToCart">
            در حال افزودن...
        </span>
    </button>
</div>
