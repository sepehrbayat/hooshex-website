<?php

declare(strict_types=1);

namespace App\Interactions\Events;

use App\Interactions\Comment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Comment Created Event
 * Dispatched when a new comment is created
 */
class CommentCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Comment $comment
    ) {}
}
