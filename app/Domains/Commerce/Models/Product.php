<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Models;

use App\Domains\Core\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use Spatie\Tags\HasTags;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;
    use HasSEO;
    use HasTags;

    protected $fillable = [
        'title',
        'slug',
        'price',
        'sale_price',
        'short_description',
        'description',
        'sku',
        'is_digital',
        'file_url',
        'thumbnail_path',
        'thumbnail_id',
        'stock_status',
        'stock_quantity',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'is_digital' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'price' => 'integer',
        'sale_price' => 'integer',
        'stock_quantity' => 'integer',
    ];

    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'thumbnail_id');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'sku' => $this->sku,
        ];
    }
}