<?php

declare(strict_types=1);

namespace App\Observers;

use App\Interactions\Review;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        $this->syncRating($review);
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        // Only recalculate if status or rating changed
        if ($review->wasChanged(['status', 'rating'])) {
            $this->syncRating($review);
        }
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        $this->syncRating($review);
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        $this->syncRating($review);
    }

    /**
     * Recalculate and sync average rating to the reviewable model.
     */
    protected function syncRating(Review $review): void
    {
        $reviewable = $review->reviewable;
        
        if (!$reviewable) {
            return;
        }

        // Calculate average rating from approved reviews only
        $averageRating = Review::where('reviewable_type', get_class($reviewable))
            ->where('reviewable_id', $reviewable->id)
            ->where('status', 'approved')
            ->avg('rating');

        // Update the rating field if it exists
        if (in_array('rating', $reviewable->getFillable()) || $reviewable->isFillable('rating')) {
            $reviewable->updateQuietly([
                'rating' => $averageRating ? round((float) $averageRating, 1) : null,
            ]);
        }
    }
}
