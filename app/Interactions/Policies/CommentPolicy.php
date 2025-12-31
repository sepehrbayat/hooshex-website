<?php

declare(strict_types=1);

namespace App\Interactions\Policies;

use App\Domains\Auth\Models\User;
use App\Interactions\Comment;
use App\Enums\UserRole;

/**
 * Comment Policy
 * Authorization rules for Comments
 */
class CommentPolicy
{
    /**
     * Determine whether anyone can view approved comments
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the comment
     */
    public function view(?User $user, Comment $comment): bool
    {
        // Approved comments are public
        if ($comment->isApproved()) {
            return true;
        }

        // User can see their own comments regardless of status
        if ($user && $comment->user_id === $user->id) {
            return true;
        }

        // Admin can see all comments
        return $user?->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can create comments
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can comment
    }

    /**
     * Determine whether the user can update the comment
     */
    public function update(User $user, Comment $comment): bool
    {
        // Admin can update any comment
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // User can update their own pending comment
        return $comment->user_id === $user->id && $comment->status === 'pending';
    }

    /**
     * Determine whether the user can delete the comment
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Admin can delete any comment
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // User can delete their own comment
        return $comment->user_id === $user->id;
    }

    /**
     * Determine whether the user can approve comments
     */
    public function approve(User $user, Comment $comment): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can reply to the comment
     */
    public function reply(User $user, Comment $comment): bool
    {
        // Can only reply to approved comments
        return $comment->isApproved();
    }
}
