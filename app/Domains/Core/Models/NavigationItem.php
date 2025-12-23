<?php

declare(strict_types=1);

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationItem extends Model
{
    use HasFactory;

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
            return route($this->route);
        }
        return $this->url ?? '#';
    }

    public static function getMenu(string $location): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('menu_location', $location)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();
    }
}
