<?php

declare(strict_types=1);

namespace App\Domains\Core\Actions;

use App\Domains\Core\Models\Redirect;

/**
 * Handle Redirect Action
 * Checks for redirects and handles 301/302 redirects
 */
class HandleRedirectAction
{
    /**
     * Check if a redirect exists for the given URL
     *
     * @param string $sourceUrl The URL to check
     * @return Redirect|null
     */
    public function findRedirect(string $sourceUrl): ?Redirect
    {
        // Normalize the URL (remove trailing slash, lowercase)
        $normalizedUrl = rtrim(strtolower($sourceUrl), '/');

        $redirect = Redirect::where('source_url', $normalizedUrl)->first();

        if ($redirect) {
            $redirect->recordHit();
        }

        return $redirect;
    }

    /**
     * Create a new redirect
     */
    public function createRedirect(string $sourceUrl, string $targetUrl, int $statusCode = 301): Redirect
    {
        return Redirect::create([
            'source_url' => rtrim(strtolower($sourceUrl), '/'),
            'target_url' => $targetUrl,
            'status_code' => $statusCode,
        ]);
    }

    /**
     * Get redirect URL if exists
     */
    public function getRedirectUrl(string $sourceUrl): ?array
    {
        $redirect = $this->findRedirect($sourceUrl);

        if (!$redirect) {
            return null;
        }

        return [
            'url' => $redirect->target_url,
            'status_code' => $redirect->status_code,
        ];
    }
}
