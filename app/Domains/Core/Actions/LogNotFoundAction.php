<?php

declare(strict_types=1);

namespace App\Domains\Core\Actions;

use App\Domains\Core\Models\NotFoundLog;

/**
 * Log Not Found Action
 * Records 404 errors for analysis
 */
class LogNotFoundAction
{
    /**
     * Log a 404 error
     *
     * @param string $url The URL that was not found
     * @param string|null $referer The referring URL
     * @param string|null $ipAddress The client IP address
     * @param string|null $userAgent The client user agent
     * @return NotFoundLog
     */
    public function execute(
        string $url,
        ?string $referer = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): NotFoundLog {
        // Check if this URL has been logged before
        $existing = NotFoundLog::where('url', $url)->first();

        if ($existing) {
            // Increment hit count
            $existing->increment('hit_count');
            $existing->update(['last_seen_at' => now()]);
            return $existing;
        }

        // Create new log entry
        return NotFoundLog::create([
            'url' => $url,
            'referer' => $referer,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'hit_count' => 1,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
    }
}
