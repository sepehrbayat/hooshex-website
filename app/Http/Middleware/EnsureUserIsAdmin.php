<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to login and auth routes (before authentication)
        $authRoutes = [
            'filament.admin.auth.login',
            'filament.admin.auth.logout',
            'filament.admin.auth.password-reset.request',
            'filament.admin.auth.password-reset.reset',
            'filament.admin.auth.email-verification.prompt',
            'filament.admin.auth.email-verification.verify',
        ];

        if ($request->routeIs($authRoutes) || str_contains($request->path(), '/admin/login')) {
            return $next($request);
        }

        // Check if user is authenticated and is admin
        if (!auth()->check() || !auth()->user()?->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        return $next($request);
    }
}

