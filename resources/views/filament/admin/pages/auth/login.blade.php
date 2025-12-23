@php
    $heading = $this->getHeading();
    $subheading = $this->getSubheading();
@endphp

<x-filament-panels::page.simple>
    @push('styles')
    <style>
        /* Custom background overlay */
        body {
            background: linear-gradient(135deg, #FCF1FB 0%, rgba(119, 95, 238, 0.1) 50%, rgba(235, 85, 200, 0.2) 100%) !important;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            right: 0;
            width: 24rem;
            height: 24rem;
            background: rgba(119, 95, 238, 0.1);
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: 0;
            left: 0;
            width: 24rem;
            height: 24rem;
            background: rgba(235, 85, 200, 0.2);
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        .fi-main {
            position: relative;
            z-index: 1;
        }

        /* Logo and Brand Section */
        .fi-login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .fi-login-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 5rem;
            height: 5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #775FEE 0%, #EB55C8 100%);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(119, 95, 238, 0.3);
        }

        .fi-login-title {
            font-family: 'Vazirmatn', sans-serif !important;
            font-feature-settings: 'ss01' on, 'ss02' on, 'ss03' on, 'ss04' on !important;
            font-variation-settings: 'DOTS' 7 !important;
            font-size: 1.875rem;
            font-weight: 900;
            color: #22165E;
            margin-bottom: 0.5rem;
        }

        .fi-login-subtitle {
            font-family: 'Vazirmatn', sans-serif !important;
            font-size: 1rem;
            color: #AAAAAA;
        }

        /* Form Card */
        .fi-section-content-ctn {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(20px) !important;
            border-radius: 1rem !important;
            box-shadow: 0px 6px 24px rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            padding: 2rem !important;
        }

        /* Input Styles */
        [dir="rtl"] .fi-input-wrp input {
            text-align: right;
        }
        
        [dir="rtl"] .fi-input-wrp input[dir="ltr"] {
            text-align: left;
        }

        .fi-input-wrp {
            background: rgba(119, 95, 238, 0.1) !important;
            border-radius: 12px !important;
            border: 1px solid rgba(119, 95, 238, 0.2) !important;
            transition: all 0.3s ease !important;
        }

        .fi-input-wrp:focus-within {
            background: rgba(119, 95, 238, 0.15) !important;
            border-color: #775FEE !important;
            box-shadow: 0 0 0 3px rgba(119, 95, 238, 0.1) !important;
        }

        .fi-input-wrp input {
            font-family: 'Vazirmatn', sans-serif !important;
        }

        .fi-label {
            font-family: 'Vazirmatn', sans-serif !important;
            font-weight: 500 !important;
            color: #22165E !important;
        }

        /* Button Styles */
        .fi-btn-primary {
            background: linear-gradient(135deg, #775FEE 0%, #5537EA 100%) !important;
            box-shadow: 0px 2px 8px rgba(235, 85, 200, 0.46), inset 0px -4px 16px rgba(102, 24, 84, 0.13), inset 0px 4px 16px rgba(102, 24, 84, 0.11) !important;
            border-radius: 8px !important;
            font-family: 'Vazirmatn', sans-serif !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            width: 100%;
        }

        .fi-btn-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0px 4px 12px rgba(235, 85, 200, 0.6), inset 0px -4px 16px rgba(102, 24, 84, 0.13), inset 0px 4px 16px rgba(102, 24, 84, 0.11) !important;
        }

        /* Footer */
        .fi-login-footer {
            margin-top: 1.5rem;
            text-align: center;
        }

        .fi-login-footer p {
            font-family: 'Vazirmatn', sans-serif !important;
            font-size: 0.875rem;
            color: #AAAAAA;
        }
    </style>
    @endpush

    <x-slot name="heading">
        <div class="fi-login-header">
            <div class="fi-login-logo">
                <span class="text-3xl font-black text-white" style="font-family: 'Vazirmatn', sans-serif; font-feature-settings: 'ss01' on, 'ss02' on, 'ss03' on, 'ss04' on; font-variation-settings: 'DOTS' 7;">هو</span>
            </div>
            <h1 class="fi-login-title">{{ $heading }}</h1>
            @if($subheading)
                <p class="fi-login-subtitle">{{ $subheading }}</p>
            @endif
        </div>
    </x-slot>

    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.actions.register.before') }}

            {{ $this->registerAction }}
        </x-slot>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

    <div class="fi-login-footer">
        <p>© {{ date('Y') }} هوشکس. تمامی حقوق محفوظ است.</p>
    </div>
</x-filament-panels::page.simple>

