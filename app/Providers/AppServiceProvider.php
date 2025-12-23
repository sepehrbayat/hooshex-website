<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\LegacyUserProvider;
use App\Http\ViewComposers\LayoutComposer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\HorizonServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(HorizonServiceProvider::class);

        // Telescope temporarily disabled due to database connection issues
        // if ($this->app->environment('local')) {
        //     $this->app->register(TelescopeServiceProvider::class);
        // }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::provider('legacy-eloquent', function ($app, array $config) {
            return new LegacyUserProvider($app['hash'], $config['model']);
        });

        // Register View Composers for shared data
        View::composer('components.layouts.app', LayoutComposer::class);

        // Share GeneralSettings with all views
        View::share('settings', app(\App\Settings\GeneralSettings::class));
    }
}
