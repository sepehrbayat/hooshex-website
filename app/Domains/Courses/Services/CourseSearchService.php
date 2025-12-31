<?php

declare(strict_types=1);

namespace App\Domains\Courses\Services;

use App\Domains\Courses\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Enums\CourseStatus;

/**
 * Course Search Service
 * Handles searching and filtering courses with caching
 */
class CourseSearchService
{
    /**
     * Cache TTL for featured/popular queries (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Search courses (not cached due to dynamic filters)
     */
    public function search(
        ?string $query = null,
        array $filters = [],
        int $perPage = 12
    ): LengthAwarePaginator {
        $builder = Course::query()
            ->where('status', CourseStatus::Published);

        // Apply search query
        if ($query) {
            $builder->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }

        // Apply filters
        $builder = $this->applyFilters($builder, $filters);

        // Apply sorting
        $builder = $this->applySorting($builder, $filters['sort'] ?? 'latest');

        return $builder->with(['teacher.teacherProfile', 'thumbnail'])->paginate($perPage);
    }

    /**
     * Get featured courses with caching
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return Cache::remember(
            "courses:featured:{$limit}",
            self::CACHE_TTL,
            fn() => Course::query()
                ->where('status', CourseStatus::Published)
                ->where('is_featured', true)
                ->with(['teacher.teacherProfile', 'thumbnail'])
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get popular courses by enrollment count with caching
     */
    public function getPopular(int $limit = 6): Collection
    {
        return Cache::remember(
            "courses:popular:{$limit}",
            self::CACHE_TTL,
            fn() => Course::query()
                ->where('status', CourseStatus::Published)
                ->withCount('enrollments')
                ->orderByDesc('enrollments_count')
                ->with(['teacher.teacherProfile', 'thumbnail'])
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get related courses by category (short cache)
     */
    public function getRelated(Course $course, int $limit = 4): Collection
    {
        $categoryIds = $course->categories()->pluck('categories.id');

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return Cache::remember(
            "courses:related:{$course->id}:{$limit}",
            1800, // 30 minutes
            fn() => Course::query()
                ->where('status', CourseStatus::Published)
                ->where('id', '!=', $course->id)
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                })
                ->with(['teacher.teacherProfile', 'thumbnail'])
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Clear all courses cache
     */
    public static function clearCache(): void
    {
        foreach ([3, 4, 6, 8, 12] as $limit) {
            Cache::forget("courses:featured:{$limit}");
            Cache::forget("courses:popular:{$limit}");
        }
    }

    /**
     * Apply filters to query builder
     */
    private function applyFilters(Builder $builder, array $filters): Builder
    {
        // Filter by level
        if (!empty($filters['level'])) {
            $builder->where('level', $filters['level']);
        }

        // Filter by category
        if (!empty($filters['category'])) {
            $builder->whereHas('categories', function ($query) use ($filters) {
                $query->where('slug', $filters['category']);
            });
        }

        // Filter by teacher
        if (!empty($filters['teacher_id'])) {
            $builder->where('teacher_id', $filters['teacher_id']);
        }

        // Filter by price (free/paid)
        if (isset($filters['free']) && $filters['free']) {
            $builder->where('price', 0);
        }

        // Filter by certificate available
        if (isset($filters['has_certificate']) && $filters['has_certificate']) {
            $builder->where('is_certificate_available', true);
        }

        return $builder;
    }

    /**
     * Apply sorting to query builder
     */
    private function applySorting(Builder $builder, string $sort): Builder
    {
        return match ($sort) {
            'popular' => $builder->withCount('enrollments')->orderByDesc('enrollments_count'),
            'price_low' => $builder->orderBy('price'),
            'price_high' => $builder->orderByDesc('price'),
            'duration' => $builder->orderByDesc('duration'),
            default => $builder->orderByDesc('published_at'),
        };
    }
}
