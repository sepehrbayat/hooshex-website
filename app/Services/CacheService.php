<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Centralized caching service
 * Uses Redis cache tags for efficient invalidation
 */
class CacheService
{
    /**
     * Cache key prefixes
     */
    private const PREFIX_COURSES = 'courses';
    private const PREFIX_CATEGORIES = 'categories';
    private const PREFIX_SETTINGS = 'settings';

    /**
     * Cache duration in seconds
     */
    private const DURATION_SHORT = 300; // 5 minutes
    private const DURATION_MEDIUM = 3600; // 1 hour
    private const DURATION_LONG = 86400; // 24 hours

    /**
     * Get featured courses with caching
     */
    public static function getFeaturedCourses(int $limit = 6): array
    {
        return Cache::tags(['courses', 'featured'])->remember(
            self::PREFIX_COURSES . ':featured:' . $limit,
            self::DURATION_MEDIUM,
            fn () => \App\Domains\Courses\Models\Course::query()
                ->where('status', 'published')
                ->where('is_featured', true)
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get()
                ->toArray()
        );
    }

    /**
     * Get popular courses with caching
     */
    public static function getPopularCourses(int $limit = 6): array
    {
        return Cache::tags(['courses', 'popular'])->remember(
            self::PREFIX_COURSES . ':popular:' . $limit,
            self::DURATION_MEDIUM,
            fn () => \App\Domains\Courses\Models\Course::query()
                ->where('status', 'published')
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get()
                ->toArray()
        );
    }

    /**
     * Get categories by type with caching
     */
    public static function getCategoriesByType(string $type): array
    {
        return Cache::tags(['categories', $type])->remember(
            self::PREFIX_CATEGORIES . ':' . $type,
            self::DURATION_LONG,
            fn () => \App\Domains\Core\Models\Category::query()
                ->where('type', $type)
                ->orderBy('name')
                ->get()
                ->toArray()
        );
    }

    /**
     * Invalidate all course-related cache
     */
    public static function invalidateCourses(): void
    {
        Cache::tags(['courses'])->flush();
    }

    /**
     * Invalidate all category-related cache
     */
    public static function invalidateCategories(): void
    {
        Cache::tags(['categories'])->flush();
    }

    /**
     * Invalidate all cache
     */
    public static function invalidateAll(): void
    {
        Cache::flush();
    }
}

