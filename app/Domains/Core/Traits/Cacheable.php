<?php

declare(strict_types=1);

namespace App\Domains\Core\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Cacheable Trait
 * Provides caching capabilities for Eloquent models
 */
trait Cacheable
{
    /**
     * Default cache TTL in seconds (1 hour)
     */
    protected static int $defaultCacheTtl = 3600;

    /**
     * Get cache prefix for this model
     */
    public static function getCachePrefix(): string
    {
        return strtolower(class_basename(static::class));
    }

    /**
     * Get a cached result or execute the query
     *
     * @param string $key Cache key suffix
     * @param \Closure $callback Query callback
     * @param int|null $ttl Cache TTL in seconds
     * @return mixed
     */
    public static function cached(string $key, \Closure $callback, ?int $ttl = null): mixed
    {
        $fullKey = static::getCachePrefix() . ':' . $key;
        $ttl = $ttl ?? static::$defaultCacheTtl;

        return Cache::remember($fullKey, $ttl, $callback);
    }

    /**
     * Get all records with caching
     *
     * @param int|null $ttl Cache TTL in seconds
     * @return Collection
     */
    public static function allCached(?int $ttl = null): Collection
    {
        return static::cached('all', fn() => static::all(), $ttl);
    }

    /**
     * Find a record by ID with caching
     *
     * @param int|string $id
     * @param int|null $ttl Cache TTL in seconds
     * @return static|null
     */
    public static function findCached(int|string $id, ?int $ttl = null): ?static
    {
        return static::cached("id:{$id}", fn() => static::find($id), $ttl);
    }

    /**
     * Find a record by slug with caching
     *
     * @param string $slug
     * @param int|null $ttl Cache TTL in seconds
     * @return static|null
     */
    public static function findBySlugCached(string $slug, ?int $ttl = null): ?static
    {
        return static::cached("slug:{$slug}", fn() => static::where('slug', $slug)->first(), $ttl);
    }

    /**
     * Clear all cache for this model
     */
    public static function clearCache(): void
    {
        $prefix = static::getCachePrefix();
        
        // Clear known cache keys
        Cache::forget($prefix . ':all');
        Cache::forget($prefix . ':featured');
        Cache::forget($prefix . ':popular');
        
        // If using Redis, we can use pattern matching
        if (config('cache.default') === 'redis') {
            $pattern = config('cache.prefix') . ':' . $prefix . ':*';
            $keys = Cache::getRedis()->keys($pattern);
            foreach ($keys as $key) {
                Cache::getRedis()->del($key);
            }
        }
    }

    /**
     * Clear cache for a specific record
     */
    public function clearRecordCache(): void
    {
        $prefix = static::getCachePrefix();
        
        Cache::forget($prefix . ':id:' . $this->id);
        
        if (isset($this->slug)) {
            Cache::forget($prefix . ':slug:' . $this->slug);
        }
        
        // Also clear collection caches
        Cache::forget($prefix . ':all');
        Cache::forget($prefix . ':featured');
        Cache::forget($prefix . ':popular');
    }

    /**
     * Boot the trait - automatically clear cache on model events
     */
    public static function bootCacheable(): void
    {
        static::saved(function ($model) {
            $model->clearRecordCache();
        });

        static::deleted(function ($model) {
            $model->clearRecordCache();
        });
    }
}
