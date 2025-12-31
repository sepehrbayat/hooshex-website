<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Session\Middleware\StartSession as BaseStartSession;
use Illuminate\Support\Facades\Config;

class StartSessionForPanel extends BaseStartSession
{
    /**
     * Get the session cookie name.
     * Override to set different cookie names for different panels
     */
    protected function sessionCookieName(): string
    {
        $request = request();
        $path = $request->path();
        $panelId = null;
        
        // Determine panel based on request path
        if (str_starts_with($path, 'admin')) {
            $panelId = 'admin';
        } elseif (str_starts_with($path, 'app')) {
            $panelId = 'app';
        }
        
        // Use default session cookie name or panel-specific name
        $baseName = Config::get('session.cookie', 'laravel_session');
        
        return $panelId ? $baseName . '_' . $panelId : $baseName;
    }
}

