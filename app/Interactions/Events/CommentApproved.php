<?php

declare(strict_types=1);

namespace App\Interactions\Events;

use App\Interactions\Comment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Comment Approved Event
 * Dispatched when a comment is approved
 */
class CommentApproved
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Comment $comment
    ) {}
}
