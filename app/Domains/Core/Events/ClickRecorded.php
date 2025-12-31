<?php

declare(strict_types=1);

namespace App\Domains\Core\Events;

use App\Domains\Core\Models\Click;
use App\Domains\AiTools\Models\AiTool;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Click Recorded Event
 * Dispatched when an affiliate/website click is tracked
 */
class ClickRecorded
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AiTool $aiTool,
        public readonly Click $click,
    ) {}
}
