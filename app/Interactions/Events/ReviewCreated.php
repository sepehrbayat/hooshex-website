<?php

declare(strict_types=1);

namespace App\Interactions\Events;

use App\Interactions\Review;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Review Created Event
 * Dispatched when a new review is created
 */
class ReviewCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Review $review
    ) {}
}
