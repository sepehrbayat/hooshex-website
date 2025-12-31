<?php

declare(strict_types=1);

namespace App\Interactions\Actions;

use App\Domains\Auth\Models\User;
use App\Interactions\Review;
use App\Interactions\Events\ReviewCreated;
use Illuminate\Database\Eloquent\Model;

/**
 * Create Review Action
 * Handles creating a new review
 */
class CreateReviewAction
{
    /**
     * Execute the action
     *
     * @param Model $reviewable The model being reviewed (AiTool, Course, etc.)
     * @param User $user The user creating the review
     * @param int $rating Rating 1-5
     * @param string|null $title Review title
     * @param string|null $body Review body
     * @return Review
     */
    public function execute(
        Model $reviewable,
        User $user,
        int $rating,
        ?string $title = null,
        ?string $body = null
    ): Review {
        // Check for duplicate review
        $existingReview = Review::where('reviewable_type', get_class($reviewable))
            ->where('reviewable_id', $reviewable->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            // Update existing review instead
            $existingReview->update([
                'rating' => $rating,
                'title' => $title,
                'body' => $body,
                'status' => 'pending',
            ]);
            return $existingReview;
        }

        $review = new Review([
            'user_id' => $user->id,
            'rating' => $rating,
            'title' => $title,
            'body' => $body,
            'status' => 'pending', // Requires moderation
        ]);

        $reviewable->reviews()->save($review);

        event(new ReviewCreated($review));

        return $review;
    }
}
