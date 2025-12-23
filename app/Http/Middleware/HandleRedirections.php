<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\Core\Models\Redirect;
use App\Domains\Core\Models\NotFoundLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirections
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip admin, API, and asset routes
        if (
            $request->is('admin*') ||
            $request->is('api*') ||
            $request->is('_ignition*') ||
            $request->is('*.css') ||
            $request->is('*.js') ||
            $request->is('*.jpg') ||
            $request->is('*.png') ||
            $request->is('*.gif') ||
            $request->is('*.svg')
        ) {
            return $next($request);
        }

        // Check for redirect
        $path = '/' . trim($request->path(), '/');
        $redirect = Redirect::where('source_url', $path)->first();

        if ($redirect) {
            $redirect->recordHit();
            
            return redirect($redirect->target_url, $redirect->status_code);
        }

        // Let the request continue, we'll handle 404 in exception handler
        $response = $next($request);

        // If we got a 404, log it
        if ($response->status() === 404 && !$request->is('admin*')) {
            $this->logNotFound($request);
        }

        return $response;
    }

    protected function logNotFound(Request $request): void
    {
        $url = $request->fullUrl();
        
        $log = NotFoundLog::firstOrNew(['url' => $url]);
        
        if ($log->exists) {
            $log->increment('hit_count');
            $log->last_seen_at = now();
        } else {
            $log->referer = $request->header('referer');
            $log->ip_address = $request->ip();
            $log->user_agent = $request->userAgent();
            $log->hit_count = 1;
            $log->first_seen_at = now();
            $log->last_seen_at = now();
        }
        
        $log->save();
    }
}