<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Http\Middleware\AuthenticateSessionForAppPanel;
use App\Http\Middleware\StartSessionForPanel;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->authGuard('app')
            ->brandName('پنل کاربری')
            ->login(\App\Filament\App\Pages\Auth\Login::class)
            ->font('Vazirmatn')
            ->darkMode(false)
            ->colors([
                'primary' => [
                    50 => 'rgba(119, 95, 238, 0.1)',
                    100 => 'rgba(119, 95, 238, 0.2)',
                    200 => 'rgba(119, 95, 238, 0.3)',
                    300 => 'rgba(119, 95, 238, 0.4)',
                    400 => '#442CBB',
                    500 => '#775FEE',
                    600 => '#5537EA',
                    700 => '#442CBB',
                    800 => '#22165E',
                    900 => '#1A1047',
                    950 => '#0D0823',
                ],
                'gray' => [
                    50 => '#FCF1FB',
                    100 => '#F5F5F5',
                    200 => '#EEEEEE',
                    300 => '#E0E0E0',
                    400 => '#BDBDBD',
                    500 => '#9E9E9E',
                    600 => '#757575',
                    700 => '#616161',
                    800 => '#2D2D2D',
                    900 => '#22165E',
                    950 => '#1A1047',
                ],
            ])
            ->discoverPages(
                in: app_path('Filament/App/Pages'),
                for: 'App\\Filament\\App\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/App/Widgets'),
                for: 'App\\Filament\\App\\Widgets'
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSessionForPanel::class,
                // AuthenticateSessionForAppPanel::class, // REMOVED: Causes 403 logout after cache clear with legacy password migration
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_START,
                fn (): string => view('components.filament.app-panel-styles')->render()
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => view('components.filament.rtl-styles')->render()
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): string => view('components.filament.choices-rtl-script')->render()
            );
    }
}