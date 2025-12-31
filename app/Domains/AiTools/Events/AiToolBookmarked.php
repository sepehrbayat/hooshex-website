<?php

declare(strict_types=1);

namespace App\Domains\AiTools\Events;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * AI Tool Bookmarked Event
 * Dispatched when a user bookmarks an AI tool
 */
class AiToolBookmarked
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AiTool $aiTool,
        public readonly User $user,
        public readonly bool $bookmarked, // true = added, false = removed
    ) {}
}
