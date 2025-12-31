<?php

declare(strict_types=1);

namespace App\Domains\Blog\Events;

use App\Domains\Blog\Models\Post;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Post Viewed Event
 * Dispatched when a post page is viewed
 */
class PostViewed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Post $post,
        public readonly ?User $user = null,
        public readonly ?string $ipAddress = null,
    ) {}
}
