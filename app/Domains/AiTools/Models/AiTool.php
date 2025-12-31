<?php

declare(strict_types=1);

namespace App\Domains\AiTools\Models;

use App\Domains\Courses\Models\Course;
use App\Enums\PricingType;
use App\Domains\Core\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Schema\CustomSchema;

class AiTool extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;
    use HasSEO;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'content',
        'website_url',
        'affiliate_url',
        'deal_url',
        'demo_url',
        'logo_path',
        'logo_id',
        'gallery_ids',
        'pricing_type',
        'price',
        'rating',
        'users_count',
        'success_rate',
        'response_time',
        'languages',
        'features',
        'pros',
        'cons',
        'company',
        'is_verified',
        'is_featured',
        'has_course',
        'related_course_id',
        'published_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'has_course' => 'boolean',
        'languages' => 'array',
        'features' => 'array',
        'gallery_ids' => 'array',
        'pros' => 'array',
        'cons' => 'array',
        'published_at' => 'datetime',
        'rating' => 'float',
        'success_rate' => 'float',
        'price' => 'integer',
        'pricing_type' => PricingType::class,
    ];

    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    public function relatedCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'related_course_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(\App\Domains\Core\Models\Click::class, 'ai_tool_id');
    }

    public function bookmarkers(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Domains\Auth\Models\User::class,
            'bookmarks',
            'ai_tool_id',
            'user_id'
        );
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(\App\Interactions\Review::class, 'reviewable');
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'logo_id');
    }

    /**
     * Get gallery media collection from gallery_ids
     */
    public function getGalleryMediaAttribute()
    {
        if (empty($this->gallery_ids)) {
            return collect();
        }

        return \Awcodes\Curator\Models\Media::whereIn('id', $this->gallery_ids)->get();
    }

    /**
     * Get dynamic SEO data for the AI tool
     * This method is called by the SEO package to get custom SEO data
     * 
     * IMPORTANT: This method is defensive to prevent issues during table rendering.
     * It only accesses relationships if they're already loaded to avoid N+1 queries.
     */
    public function getDynamicSEOData(): SEOData
    {
        // Prevent execution during Filament admin table rendering to avoid timeouts
        // Check if we're in an admin context (Filament routes typically contain '/admin')
        if (request()->is('admin/*') || request()->is('*/admin/*')) {
            // Return minimal SEO data to prevent relationship access during table rendering
            return new SEOData(
                title: $this->seo_title ?? $this->name,
                description: $this->seo_description ?? $this->short_description,
                type: 'website',
            );
        }

        // Build SoftwareApplication schema
        $softwareSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $this->name,
            'description' => $this->short_description ?? $this->content,
            'applicationCategory' => 'WebApplication',
            'operatingSystem' => 'Web',
        ];

        // Add offers schema based on pricing_type
        if ($this->pricing_type) {
            $offer = [
                '@type' => 'Offer',
                'priceCurrency' => 'IRR',
            ];

            switch ($this->pricing_type) {
                case PricingType::Free:
                    $offer['price'] = '0';
                    $offer['availability'] = 'https://schema.org/InStock';
                    break;
                case PricingType::Paid:
                    if ($this->price) {
                        $offer['price'] = (string) $this->price;
                        $offer['availability'] = 'https://schema.org/InStock';
                    }
                    break;
                case PricingType::Freemium:
                    $offer['price'] = '0';
                    $offer['availability'] = 'https://schema.org/InStock';
                    break;
                case PricingType::FreeTrial:
                    $offer['price'] = '0';
                    $offer['availability'] = 'https://schema.org/InStock';
                    break;
                case PricingType::Contact:
                    // No price for contact-based pricing
                    break;
            }

            if (isset($offer['price'])) {
                $softwareSchema['offers'] = $offer;
            }
        }

        // Add logo/image - only if relationship is already loaded to avoid N+1 queries
        if ($this->relationLoaded('logo') && $this->logo) {
            $softwareSchema['image'] = $this->logo->url;
        } elseif ($this->logo_id && !$this->relationLoaded('logo')) {
            // If logo_id exists but relationship not loaded, skip to avoid query
            // This prevents N+1 queries during table rendering
        }

        // Add to schema collection
        $schemaCollection = SchemaCollection::initialize();
        $schemaCollection->push(new CustomSchema($softwareSchema));

        // Get logo URL safely - only if already loaded
        $logoUrl = null;
        if ($this->relationLoaded('logo') && $this->logo) {
            $logoUrl = $this->logo->url;
        }

        // Create SEOData with custom fields
        $seoData = new SEOData(
            title: $this->seo_title ?? $this->name,
            description: $this->seo_description ?? $this->short_description,
            image: $logoUrl,
            url: route('ai-tools.show', $this->slug),
            schema: $schemaCollection,
            type: 'website',
            published_time: $this->published_at,
            modified_time: $this->updated_at,
        );

        return $seoData;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'pricing_type' => $this->pricing_type?->value,
            'features' => $this->features,
            'company' => $this->company,
        ];
    }
}

