<?php

declare(strict_types=1);

namespace App\Observers;

use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Services\CourseSearchService;
use Illuminate\Support\Facades\Cache;

/**
 * Observer to invalidate Course cache on model changes
 */
class CourseCacheObserver
{
    /**
     * Handle the Course "saved" event (created or updated).
     */
    public function saved(Course $course): void
    {
        $this->clearCache($course);
    }

    /**
     * Handle the Course "deleted" event.
     */
    public function deleted(Course $course): void
    {
        $this->clearCache($course);
    }

    /**
     * Clear caches related to this Course
     */
    private function clearCache(Course $course): void
    {
        // Clear featured/popular caches
        CourseSearchService::clearCache();

        // Clear related cache for this course
        foreach ([3, 4, 6, 8] as $limit) {
            Cache::forget("courses:related:{$course->id}:{$limit}");
        }
    }
}
