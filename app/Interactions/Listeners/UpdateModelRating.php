<?php

declare(strict_types=1);

namespace App\Interactions\Listeners;

use App\Interactions\Events\ReviewCreated;
use App\Interactions\Review;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Update Model Rating Listener
 * Updates the average rating on the reviewable model after review is approved
 */
class UpdateModelRating implements ShouldQueue
{
    /**
     * Handle the event
     */
    public function handle(ReviewCreated $event): void
    {
        $review = $event->review;

        // Log the new review
        Log::info('New review submitted', [
            'review_id' => $review->id,
            'user_id' => $review->user_id,
            'reviewable_type' => $review->reviewable_type,
            'reviewable_id' => $review->reviewable_id,
            'rating' => $review->rating,
        ]);

        // Calculate and update average rating on the model
        // This is done only for approved reviews
        $this->updateAverageRating($review);
    }

    /**
     * Update the average rating on the reviewable model
     */
    private function updateAverageRating(Review $review): void
    {
        $reviewable = $review->reviewable;

        if (!$reviewable) {
            return;
        }

        // Check if the model has a rating column
        if (!in_array('rating', $reviewable->getFillable())) {
            return;
        }

        // Calculate average from approved reviews
        $averageRating = Review::where('reviewable_type', get_class($reviewable))
            ->where('reviewable_id', $reviewable->id)
            ->where('status', 'approved')
            ->avg('rating');

        if ($averageRating !== null) {
            $reviewable->update(['rating' => round($averageRating, 1)]);
        }
    }
}
