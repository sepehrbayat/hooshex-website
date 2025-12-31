<?php

declare(strict_types=1);

namespace App\Domains\Blog\Actions;

use App\Domains\Blog\Events\PostPublished;
use App\Domains\Blog\Models\Post;
use App\Enums\PostStatus;

/**
 * Publish Post Action
 * Handles publishing a blog post
 */
class PublishPostAction
{
    /**
     * Execute the action
     *
     * @param Post $post The post to publish
     * @param \DateTimeInterface|null $publishAt Optional scheduled publish time
     * @return Post
     */
    public function execute(Post $post, ?\DateTimeInterface $publishAt = null): Post
    {
        $wasPublished = $post->status === PostStatus::Published;

        $post->status = PostStatus::Published;
        $post->published_at = $publishAt ?? now();
        $post->save();

        // Dispatch event if this is the first time being published
        if (!$wasPublished) {
            event(new PostPublished($post));
        }

        return $post;
    }

    /**
     * Schedule a post for future publication
     */
    public function schedule(Post $post, \DateTimeInterface $publishAt): Post
    {
        $post->status = PostStatus::Scheduled;
        $post->published_at = $publishAt;
        $post->save();

        return $post;
    }

    /**
     * Unpublish a post (set to draft)
     */
    public function unpublish(Post $post): Post
    {
        $post->status = PostStatus::Draft;
        $post->save();

        return $post;
    }
}
