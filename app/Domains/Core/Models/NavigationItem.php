<?php

declare(strict_types=1);

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class NavigationItem extends Model
{
    use HasFactory;

    /**
     * Cache TTL for navigation menus (24 hours)
     */
    private const CACHE_TTL = 86400;

    protected $fillable = [
        'menu_location',
        'label',
        'url',
        'route',
        'icon',
        'sort_order',
        'parent_id',
        'is_active',
        'open_in_new_tab',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Clear cache when navigation items are modified
        static::saved(fn() => static::clearMenuCache());
        static::deleted(fn() => static::clearMenuCache());
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function getHrefAttribute(): string
    {
        if ($this->route) {
            try {
                return route($this->route);
            } catch (\Exception $e) {
                return $this->url ?? '#';
            }
        }
        return $this->url ?? '#';
    }

    /**
     * Get menu items for a location with caching
     */
    public static function getMenu(string $location): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            "navigation:menu:{$location}",
            self::CACHE_TTL,
            fn() => static::query()
                ->where('menu_location', $location)
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->with('children')
                ->orderBy('sort_order')
                ->get()
        );
    }

    /**
     * Clear all navigation menu caches
     */
    public static function clearMenuCache(): void
    {
        Cache::forget('navigation:menu:header');
        Cache::forget('navigation:menu:footer');
    }
}
