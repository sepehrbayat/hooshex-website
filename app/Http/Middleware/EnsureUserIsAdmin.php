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
        // At this point, user is already authenticated (Authenticate middleware ran first)
        // Just check if the authenticated user has admin role using the admin guard
        if (!auth('admin')->user()?->isAdmin()) {
            abort(403, 'دسترسی غیرمجاز. شما اجازه دسترسی به پنل مدیریت را ندارید.');
        }

        return $next($request);
    }
}

