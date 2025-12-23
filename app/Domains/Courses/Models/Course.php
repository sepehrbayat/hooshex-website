<?php

declare(strict_types=1);

namespace App\Domains\Courses\Models;

use App\Domains\Auth\Models\User;
use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Schema\CustomSchema;

class Course extends Model
{
    use HasFactory;
    use HasSEO;

    protected $fillable = [
        'teacher_id',
        'title',
        'slug',
        'short_description',
        'description',
        'price',
        'sale_price',
        'sku',
        'level',
        'language',
        'students_count',
        'is_certificate_available',
        'guarantee_text',
        'intro_video_provider',
        'intro_video_id',
        'prerequisites',
        'target_audience',
        'thumbnail_id',
        'duration',
        'status',
        'is_featured',
        'seo_title',
        'seo_description',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_certificate_available' => 'boolean',
        'published_at' => 'datetime',
        'status' => CourseStatus::class,
        'level' => CourseLevel::class,
        'prerequisites' => 'array',
        'target_audience' => 'array',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id')
            ->with('teacherProfile');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(\App\Interactions\Review::class, 'reviewable');
    }

    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'thumbnail_id');
    }

    /**
     * Get responsive embed HTML for intro video
     */
    public function getEmbedHtmlAttribute(): string
    {
        if ($this->intro_video_provider === 'aparat' && $this->intro_video_id) {
            return sprintf(
                '<style>.h_iframe-aparat_embed_frame{position:relative;}.h_iframe-aparat_embed_frame .ratio{display:block;width:100%%;height:auto;}.h_iframe-aparat_embed_frame iframe{position:absolute;top:0;left:0;width:100%%;height:100%%;}</style><div class="h_iframe-aparat_embed_frame"><span style="display: block;padding-top: 57%%"></span><iframe src="https://www.aparat.com/video/video/embed/videohash/%s/vt/frame" allowFullScreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe></div>',
                $this->intro_video_id
            );
        }

        // Add YouTube and host support if needed in the future
        return '';
    }

    /**
     * Get dynamic SEO data for the course
     * This method is called by the SEO package to get custom SEO data
     */
    public function getDynamicSEOData(): SEOData
    {
        // Build Course schema
        $courseSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $this->title,
            'description' => $this->short_description ?? $this->description,
            'provider' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ],
        ];

        // Add offers schema
        if ($this->price > 0) {
            $offerPrice = $this->sale_price ?? $this->price;
            $courseSchema['offers'] = [
                '@type' => 'Offer',
                'price' => (string) $offerPrice,
                'priceCurrency' => 'IRR',
                'availability' => 'https://schema.org/InStock',
            ];
        }

        // Add teacher/instructor
        if ($this->teacher) {
            $courseSchema['instructor'] = [
                '@type' => 'Person',
                'name' => $this->teacher->name,
            ];
        }

        // Add to schema collection
        $schemaCollection = SchemaCollection::initialize();
        $schemaCollection->push(new CustomSchema($courseSchema));

        // Create SEOData with custom fields
        $seoData = new SEOData(
            title: $this->seo_title ?? $this->title,
            description: $this->seo_description ?? $this->short_description,
            image: $this->thumbnail?->url,
            url: route('courses.show', $this->slug),
            schema: $schemaCollection,
            type: 'video.other', // For video content
            published_time: $this->published_at,
            modified_time: $this->updated_at,
        );

        // Note: og:video is handled by the SEO package through custom tags
        // We can add it via transformers if needed, but for now schema is the main focus

        return $seoData;
    }
}

