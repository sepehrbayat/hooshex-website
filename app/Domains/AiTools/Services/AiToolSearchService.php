<?php

declare(strict_types=1);

namespace App\Domains\AiTools\Services;

use App\Domains\AiTools\Models\AiTool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * AI Tool Search Service
 * Handles searching and filtering AI tools with caching
 */
class AiToolSearchService
{
    /**
     * Cache TTL for featured/popular queries (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Search AI tools (not cached due to dynamic filters)
     */
    public function search(
        ?string $query = null,
        array $filters = [],
        int $perPage = 12
    ): LengthAwarePaginator {
        $builder = AiTool::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        // Apply search query using Scout
        if ($query) {
            $ids = AiTool::search($query)->keys()->all();
            if (!empty($ids)) {
                $builder->whereIn('id', $ids);
            }
        }

        // Apply filters
        $builder = $this->applyFilters($builder, $filters);

        // Apply sorting
        $builder = $this->applySorting($builder, $filters['sort'] ?? 'latest');

        return $builder->paginate($perPage);
    }

    /**
     * Get featured AI tools with caching
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return Cache::remember(
            "aitools:featured:{$limit}",
            self::CACHE_TTL,
            fn() => AiTool::query()
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->where('is_featured', true)
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get popular AI tools by click count with caching
     */
    public function getPopular(int $limit = 6): Collection
    {
        return Cache::remember(
            "aitools:popular:{$limit}",
            self::CACHE_TTL,
            fn() => AiTool::query()
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->withCount('clicks')
                ->orderByDesc('clicks_count')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get related AI tools by category (short cache)
     */
    public function getRelated(AiTool $aiTool, int $limit = 4): Collection
    {
        $categoryIds = $aiTool->categories()->pluck('categories.id');

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return Cache::remember(
            "aitools:related:{$aiTool->id}:{$limit}",
            1800, // 30 minutes
            fn() => AiTool::query()
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->where('id', '!=', $aiTool->id)
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                })
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Clear all AI tools cache
     */
    public static function clearCache(): void
    {
        // Clear known cache patterns
        $patterns = ['aitools:featured:*', 'aitools:popular:*', 'aitools:related:*'];
        
        foreach ([3, 4, 6, 8, 12] as $limit) {
            Cache::forget("aitools:featured:{$limit}");
            Cache::forget("aitools:popular:{$limit}");
        }
    }

    /**
     * Apply filters to query builder
     */
    private function applyFilters(Builder $builder, array $filters): Builder
    {
        // Filter by pricing type
        if (!empty($filters['pricing_type'])) {
            $builder->where('pricing_type', $filters['pricing_type']);
        }

        // Filter by category
        if (!empty($filters['category'])) {
            $builder->whereHas('categories', function ($query) use ($filters) {
                $query->where('slug', $filters['category']);
            });
        }

        // Filter by verified
        if (isset($filters['verified']) && $filters['verified']) {
            $builder->where('is_verified', true);
        }

        // Filter by rating
        if (!empty($filters['min_rating'])) {
            $builder->where('rating', '>=', (float) $filters['min_rating']);
        }

        return $builder;
    }

    /**
     * Apply sorting to query builder
     */
    private function applySorting(Builder $builder, string $sort): Builder
    {
        return match ($sort) {
            'popular' => $builder->withCount('clicks')->orderByDesc('clicks_count'),
            'rating' => $builder->orderByDesc('rating'),
            'name' => $builder->orderBy('name'),
            default => $builder->orderByDesc('published_at'),
        };
    }
}
