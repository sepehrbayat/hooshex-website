<?php

declare(strict_types=1);

namespace App\Domains\Blog\Models;

use App\Domains\Core\Models\Category;
use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Enums\PostType;
use App\Enums\PostStatus;
use Laravel\Scout\Searchable;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;
    use HasSEO;
    use HasTags;

    protected $fillable = [
        'author_id',
        'type',
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail_path',
        'thumbnail_id',
        'status',
        'seo_title',
        'seo_description',
        'published_at',
        'reading_time',
        'is_featured',
        'primary_category_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'type' => PostType::class,
        'status' => PostStatus::class,
        'is_featured' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    public function primaryCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'primary_category_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(\App\Interactions\Comment::class, 'commentable');
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
            'type' => $this->type?->value,
        ];
    }

    /**
     * Get dynamic SEO data for the post
     * This method is called by the SEO package to get custom SEO data
     */
    public function getDynamicSEOData(): SEOData
    {
        // Build Article schema
        $schemaCollection = SchemaCollection::initialize();
        $schemaCollection->addArticle(function ($article, $seoData) {
            if ($this->author) {
                $article->addAuthor($this->author->name);
            }
            if ($this->primaryCategory) {
                $article->articleSection = $this->primaryCategory->name;
            }
            if ($this->updated_at) {
                $article->dateModified = $this->updated_at->toIso8601String();
            }
            return $article;
        });

        // Create SEOData with custom fields
        $seoData = new SEOData(
            title: $this->seo_title ?? $this->title,
            description: $this->seo_description ?? $this->excerpt,
            image: $this->thumbnail?->url,
            url: route('posts.show', $this->slug),
            schema: $schemaCollection,
            type: 'article',
            published_time: $this->published_at,
            modified_time: $this->updated_at,
        );

        return $seoData;
    }
}

