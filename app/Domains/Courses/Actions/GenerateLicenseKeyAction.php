<?php

declare(strict_types=1);

namespace App\Domains\Courses\Actions;

use Illuminate\Support\Str;

/**
 * Action to generate a unique license key
 */
class GenerateLicenseKeyAction
{
    /**
     * Generate a unique license key
     *
     * @return string The generated license key
     */
    public function handle(): string
    {
        // Generate a unique license key using UUID
        // Format: HOOSHEX-{UUID}-{TIMESTAMP}
        $uuid = Str::uuid()->toString();
        $timestamp = now()->format('Ymd');
        
        return sprintf('HOOSHEX-%s-%s', Str::upper(Str::substr($uuid, 0, 8)), $timestamp);
    }
}

