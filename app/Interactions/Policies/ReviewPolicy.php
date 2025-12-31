<?php

declare(strict_types=1);

namespace App\Interactions\Policies;

use App\Domains\Auth\Models\User;
use App\Interactions\Review;
use App\Enums\UserRole;

/**
 * Review Policy
 * Authorization rules for Reviews
 */
class ReviewPolicy
{
    /**
     * Determine whether anyone can view approved reviews
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the review
     */
    public function view(?User $user, Review $review): bool
    {
        // Approved reviews are public
        if ($review->isApproved()) {
            return true;
        }

        // User can see their own review regardless of status
        if ($user && $review->user_id === $user->id) {
            return true;
        }

        // Admin can see all reviews
        return $user?->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can create reviews
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can review
    }

    /**
     * Determine whether the user can update the review
     */
    public function update(User $user, Review $review): bool
    {
        // Admin can update any review
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // User can update their own review
        return $review->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the review
     */
    public function delete(User $user, Review $review): bool
    {
        // Admin can delete any review
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // User can delete their own review
        return $review->user_id === $user->id;
    }

    /**
     * Determine whether the user can approve reviews
     */
    public function approve(User $user, Review $review): bool
    {
        return $user->role === UserRole::Admin;
    }
}
