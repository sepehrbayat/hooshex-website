<?php

declare(strict_types=1);

namespace App\Domains\Blog\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Core\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use Spatie\Tags\HasTags;

class News extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;
    use HasSEO;
    use HasTags;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail_path',
        'thumbnail_id',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

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
            'excerpt' => $this->excerpt,
            'content' => $this->content,
        ];
    }
}