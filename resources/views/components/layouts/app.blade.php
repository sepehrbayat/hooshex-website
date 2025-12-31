<!DOCTYPE html>
<html lang="fa" dir="rtl" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.App = {
            isAuthenticated: @json($isAuthenticated ?? false),
            sessionCart: @json($sessionCart ?? []),
            cartCount: @json($cartCount ?? 0),
            user: @json(($isAuthenticated ?? false) ? ['name' => ($userName ?? null)] : null),
        };
    </script>
    <title>@yield('title', $title ?? ($settings->site_name ?? 'هوشکس'))</title>
    
    {{-- Favicon --}}
    @if(isset($settings->favicon_path) && $settings->favicon_path)
        <link rel="icon" href="{{ asset($settings->favicon_path) }}" type="image/x-icon">
    @endif
    
    {{-- SEO Injection --}}
    @if(isset($page) && $page)
        {!! seo()->for($page) !!}
    @else
        {!! seo() !!}
    @endif
    
    {{-- Header Scripts (Analytics, GTM, etc.) --}}
    @if(isset($settings->header_scripts) && $settings->header_scripts)
        {!! $settings->header_scripts !!}
    @endif
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body id="app" class="min-h-screen bg-surface text-text-secondary">
    <div class="flex flex-col min-h-screen">
        <header class="w-full bg-surface lg:h-auto min-h-[74px]">
            <div class="mx-auto w-full max-w-[1440px] py-4 pl-[25px] pr-[27px] md:px-14 lg:px-10 xl:px-10 lg:py-6">
                <div x-data="{ open: false }" class="flex flex-col gap-4 md:gap-0">
                    {{-- Logo and Navigation --}}
                    <div class="flex items-center justify-between">
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <span class="text-xl font-bold text-primary-800">هوشکس</span>
                        </a>
                        
                        {{-- Desktop Navigation --}}
                        <nav class="hidden md:flex items-center gap-6">
                            @if(isset($headerMenu) && $headerMenu->isNotEmpty())
                                @foreach($headerMenu as $item)
                                    <a href="{{ $item->href }}" 
                                       class="text-sm font-medium text-text-primary hover:text-primary-600"
                                       {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                                        {{ $item->label }}
                                    </a>
                                @endforeach
                            @else
                                {{-- Fallback to default navigation --}}
                                <a href="{{ route('home') }}" class="text-sm font-medium text-text-primary hover:text-primary-600">خانه</a>
                                <a href="{{ route('ai-tools.index') }}" class="text-sm font-medium text-text-primary hover:text-primary-600">ابزارهای AI</a>
                            @endif
                        </nav>
                        
                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-3">
                            @auth
                                <x-auth.profile-menu />
                                <button data-action="open-cart" class="relative p-2 text-text-primary hover:text-primary-600">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span data-cart-count class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-accent-500 text-xs font-bold text-white hidden">0</span>
                                </button>
                            @else
                                <button data-action="open-login" class="btn btn-secondary btn-sm">ورود</button>
                                <button data-action="open-login" data-next="cart" class="btn btn-primary btn-sm">ثبت‌نام</button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        {{-- Main Content --}}
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
        
        {{-- Footer --}}
        <x-footer />
    </div>
    
    {{-- Modals --}}
    <x-auth.login-modal />
    <x-cart.cart-modal />
    
    {{-- Footer Scripts (Chat widgets, etc.) --}}
    @if(isset($settings->footer_scripts) && $settings->footer_scripts)
        {!! $settings->footer_scripts !!}
    @endif
    
    @livewireScripts
</body>
</html>
