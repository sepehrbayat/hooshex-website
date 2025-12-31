<?php

declare(strict_types=1);

namespace App\Domains\AiTools\Events;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * AI Tool Viewed Event
 * Dispatched when an AI tool page is viewed
 */
class AiToolViewed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AiTool $aiTool,
        public readonly ?User $user = null,
        public readonly ?string $ipAddress = null,
    ) {}
}
