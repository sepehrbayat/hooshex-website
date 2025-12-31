<?php

declare(strict_types=1);

namespace App\Domains\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Not Found Logged Event
 * Dispatched when a 404 error is logged
 */
class NotFoundLogged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $url,
        public readonly ?string $referer = null,
        public readonly ?string $ipAddress = null,
        public readonly int $hitCount = 1,
    ) {}
}
