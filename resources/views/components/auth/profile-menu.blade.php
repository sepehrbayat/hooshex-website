@props([])

<div x-data="{ open: false }" class="relative">
    <button
        @click="open = !open"
        class="flex h-11 items-center gap-2 rounded-full bg-primary-50 px-4 text-primary-800 ring-1 ring-primary-100 transition hover:shadow-md"
        aria-label="profile menu"
    >
        <span class="text-sm font-semibold">{{ $userName ?? 'حساب کاربری' }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 0c-4.667 0-7 2.333-7 7h14c0-4.667-2.333-7-7-7Z" />
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m19 9-7 7-7-7" />
        </svg>
    </button>

    <div
        x-cloak
        x-show="open"
        @click.away="open = false"
        @keydown.escape.window="open = false"
        class="absolute left-0 z-30 mt-3 w-48 rounded-card bg-white p-3 text-sm shadow-lg ring-1 ring-primary-50"
    >
        <a href="{{ route('home') }}#dashboard" class="flex items-center justify-between rounded-btn px-3 py-2 text-primary-700 hover:bg-primary-50">
            <span>داشبورد</span>
        </a>
        <a href="{{ route('home') }}#orders" class="flex items-center justify-between rounded-btn px-3 py-2 text-primary-700 hover:bg-primary-50">
            <span>سفارش‌ها</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit" class="flex w-full items-center justify-between rounded-btn px-3 py-2 text-red-600 hover:bg-red-50">
                <span>خروج</span>
            </button>
        </form>
    </div>
</div>
