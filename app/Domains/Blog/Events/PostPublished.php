<?php

declare(strict_types=1);

namespace App\Domains\Blog\Events;

use App\Domains\Blog\Models\Post;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Post Published Event
 * Dispatched when a post is published for the first time
 */
class PostPublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Post $post
    ) {}
}
