<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession as BaseAuthenticateSession;

class AuthenticateSessionForFilament extends BaseAuthenticateSession
{
    /**
     * Get the path the user should be redirected to when their session is not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request)
    {
        if (!$request->expectsJson()) {
            // Use Filament login route instead of default 'login' route
            return route('filament.admin.auth.login');
        }

        return null;
    }
}

