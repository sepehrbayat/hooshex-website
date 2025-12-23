<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\AuthServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\Filament\AdminPanelProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        AppServiceProvider::class,
        AuthServiceProvider::class,
        EventServiceProvider::class,
        AdminPanelProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleRedirections::class,
            \App\Http\Middleware\ConvertNumbersToPersian::class,
        ]);

        $middleware->alias([
            'auth.sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'enrolled' => \App\Http\Middleware\EnsureUserIsEnrolled::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'payment/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle custom domain exceptions
        $exceptions->render(function (
            \App\Domains\Commerce\Exceptions\CartEmptyException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $e->getCode() ?: 400);
            }

            return redirect()->route('home')
                ->with('error', $e->getMessage());
        });

        $exceptions->render(function (
            \App\Domains\Courses\Exceptions\AlreadyEnrolledException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $e->getCode() ?: 409);
            }

            return redirect()->back()
                ->with('error', $e->getMessage());
        });

        $exceptions->render(function (
            \App\Domains\Commerce\Exceptions\OrderNotFoundException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $e->getCode() ?: 404);
            }

            return redirect()->route('home')
                ->with('error', $e->getMessage());
        });

        $exceptions->render(function (
            \App\Domains\Commerce\Exceptions\PaymentFailedException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $e->getCode() ?: 402);
            }

            return redirect()->back()
                ->with('error', $e->getMessage());
        });
    })->create();
