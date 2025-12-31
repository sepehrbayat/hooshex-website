<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\LegacyUserProvider;
use App\Domains\AiTools\Models\AiTool;
use App\Domains\Courses\Models\Course;
use App\Http\ViewComposers\LayoutComposer;
use App\Observers\AiToolCacheObserver;
use App\Observers\CourseCacheObserver;
use App\Livewire\Curator\CuratorPanel as CustomCuratorPanel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\HorizonServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;
use Livewire\Livewire;

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
        if ($this->app->runningInConsole()) {
            $rootMigrationBasenames = array_map(
                static fn (string $path): string => basename($path),
                glob(database_path('migrations/*.php')) ?: []
            );
            $rootMigrationBasenames = array_flip($rootMigrationBasenames);

            $migrationDirectories = array_filter(
                glob(database_path('migrations/*'), GLOB_ONLYDIR) ?: [],
                static function (string $dir) use ($rootMigrationBasenames): bool {
                    $subMigrationFiles = glob($dir . DIRECTORY_SEPARATOR . '*.php') ?: [];
                    foreach ($subMigrationFiles as $file) {
                        if (isset($rootMigrationBasenames[basename($file)])) {
                            return false;
                        }
                    }

                    return true;
                }
            );

            if ($migrationDirectories !== []) {
                $this->loadMigrationsFrom($migrationDirectories);
            }
        }

        // Increase execution time limit for web requests (development)
        if (!app()->runningInConsole()) {
            set_time_limit(300);
            ini_set('max_execution_time', '300');
        }
        
        Auth::provider('legacy-eloquent', function ($app, array $config) {
            return new LegacyUserProvider($app['hash'], $config['model']);
        });

        // Register Cache Invalidation Observers
        AiTool::observe(AiToolCacheObserver::class);
        Course::observe(CourseCacheObserver::class);

        // Curator: allow setting alt/title/caption/description during upload in the picker modal.
        Livewire::component('curator-panel', CustomCuratorPanel::class);

        // Register View Composers for shared data
        View::composer('components.layouts.app', LayoutComposer::class);

        // Share GeneralSettings with all views
        // Wrap in try-catch to prevent errors if settings are not initialized or database is unavailable
        try {
            View::share('settings', app(\App\Settings\GeneralSettings::class));
        } catch (\Exception $e) {
            // Settings not initialized yet or database error, share empty object to prevent errors
            View::share('settings', (object) []);
        }
        
        // TEMPORARILY DISABLED: Query logging might be causing performance issues
        // #region agent log - Query logging for debugging
        // if (request()->is('admin/ai-tools*')) {
        //     $logFile = base_path('.cursor/debug.log');
        //     \DB::listen(function ($query) use ($logFile) {
        //         $logData = [
        //             'sessionId' => 'debug-session',
        //             'runId' => 'run1',
        //             'hypothesisId' => 'C',
        //             'location' => 'AppServiceProvider.php:48',
        //             'message' => 'Database query executed',
        //             'data' => [
        //                 'sql' => $query->sql,
        //                 'bindings' => $query->bindings,
        //                 'time' => $query->time,
        //                 'connection' => $query->connectionName,
        //             ],
        //             'timestamp' => (int)(microtime(true) * 1000)
        //         ];
        //         file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND);
        //     });
        // }
        // #endregion
    }
}
