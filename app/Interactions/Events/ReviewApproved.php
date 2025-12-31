<?php

declare(strict_types=1);

namespace App\Interactions\Events;

use App\Interactions\Review;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Review Approved Event
 * Dispatched when a review is approved
 */
class ReviewApproved
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Review $review
    ) {}
}
