<?php

declare(strict_types=1);

namespace App\Interactions\Actions;

use App\Interactions\Review;
use App\Interactions\Events\ReviewApproved;

/**
 * Approve Review Action
 * Handles approving a review
 */
class ApproveReviewAction
{
    /**
     * Execute the action
     *
     * @param Review $review The review to approve
     * @return Review
     */
    public function execute(Review $review): Review
    {
        $review->update(['status' => 'approved']);

        event(new ReviewApproved($review));

        return $review;
    }

    /**
     * Mark review as spam
     */
    public function markAsSpam(Review $review): Review
    {
        $review->update(['status' => 'spam']);
        return $review;
    }
}
