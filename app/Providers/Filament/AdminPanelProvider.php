<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Http\Middleware\EnsureUserIsAdmin;
use Filament\Navigation\NavigationItem;
use Awcodes\Curator\CuratorPlugin;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Assets\Css;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Http\Middleware\StartSessionForPanel;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\AuthenticateSessionForFilament;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')
            ->brandName('HooshEx Admin')
            ->login(\App\Filament\Admin\Pages\Auth\Login::class)
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->font('Vazirmatn')
            ->darkMode(false)
            ->discoverResources(
                in: app_path('Filament/Admin/Resources'),
                for: 'App\\Filament\\Admin\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Admin/Pages'),
                for: 'App\\Filament\\Admin\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Admin/Widgets'),
                for: 'App\\Filament\\Admin\\Widgets'
            )
            ->navigationItems([])
            ->plugins([
                CuratorPlugin::make(),
            ])
            ->assets([
                Css::make('filament-rtl', resource_path('css/filament-rtl.css')),
                Css::make('filament-design-system', asset('css/filament-design-system.css')),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSessionForPanel::class,
                // AuthenticateSessionForFilament::class, // REMOVED: Causes 403 logout after cache clear with legacy password migration
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsAdmin::class,
            ]);
    }
}

