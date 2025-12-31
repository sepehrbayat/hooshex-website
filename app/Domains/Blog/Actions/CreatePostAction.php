<?php

declare(strict_types=1);

namespace App\Domains\Blog\Actions;

use App\Domains\Blog\Models\Post;
use App\Domains\Auth\Models\User;
use App\Enums\PostStatus;
use App\Enums\PostType;

/**
 * Create Post Action
 * Handles creating a new blog post
 */
class CreatePostAction
{
    /**
     * Execute the action
     *
     * @param array $data Post data
     * @param User $author The author
     * @return Post
     */
    public function execute(array $data, User $author): Post
    {
        $post = new Post();
        $post->author_id = $author->id;
        $post->title = $data['title'];
        $post->slug = $data['slug'] ?? \Str::slug($data['title']);
        $post->type = $data['type'] ?? PostType::Article;
        $post->status = $data['status'] ?? PostStatus::Draft;
        $post->excerpt = $data['excerpt'] ?? null;
        $post->content = $data['content'] ?? null;
        $post->thumbnail_id = $data['thumbnail_id'] ?? null;
        $post->primary_category_id = $data['primary_category_id'] ?? null;
        $post->published_at = $data['published_at'] ?? null;
        $post->save();

        // Attach categories if provided
        if (!empty($data['categories'])) {
            $post->categories()->sync($data['categories']);
        }

        // Attach tags if provided
        if (!empty($data['tags'])) {
            $post->syncTags($data['tags']);
        }

        return $post;
    }
}
