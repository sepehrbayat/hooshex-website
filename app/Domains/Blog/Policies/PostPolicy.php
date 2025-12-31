<?php

declare(strict_types=1);

namespace App\Domains\Blog\Policies;

use App\Domains\Blog\Models\Post;
use App\Domains\Auth\Models\User;
use App\Enums\PostStatus;
use App\Enums\UserRole;

/**
 * Post Policy
 * Authorization rules for Blog Posts
 */
class PostPolicy
{
    /**
     * Determine whether anyone can view the listing
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether anyone can view the post
     */
    public function view(?User $user, Post $post): bool
    {
        // Published posts are public
        if ($post->status === PostStatus::Published && 
            $post->published_at !== null && 
            $post->published_at <= now()) {
            return true;
        }

        // Drafts and scheduled posts only visible to admins and author
        if ($user === null) {
            return false;
        }

        return $user->role === UserRole::Admin || $user->id === $post->author_id;
    }

    /**
     * Determine whether the user can create posts
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Teacher]);
    }

    /**
     * Determine whether the user can update the post
     */
    public function update(User $user, Post $post): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->id === $post->author_id;
    }

    /**
     * Determine whether the user can delete the post
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->role === UserRole::Admin;
    }
}
