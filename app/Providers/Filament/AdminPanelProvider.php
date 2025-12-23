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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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
            ->navigationItems([
                NavigationItem::make('AI Tools')
                    ->url(fn () => route('filament.admin.resources.ai-tools.index'))
                    ->icon('heroicon-o-sparkles'),
            ])
            ->plugins([
                CuratorPlugin::make(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSessionForFilament::class,
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

