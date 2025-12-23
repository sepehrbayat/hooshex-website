<?php

declare(strict_types=1);

namespace App\Domains\Core\Models;

use App\Enums\WorkType;
use App\Enums\ContractType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Schema\CustomSchema;
use Spatie\Tags\HasTags;

class Career extends Model
{
    use HasFactory;
    use HasSEO;
    use HasTags;

    protected $fillable = [
        'title',
        'slug',
        'department',
        'location',
        'work_type',
        'contract_type',
        'salary_range',
        'experience_level',
        'short_description',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'application_link',
        'is_active',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'work_type' => WorkType::class,
        'contract_type' => ContractType::class,
        'responsibilities' => 'array',
        'requirements' => 'array',
        'benefits' => 'array',
    ];

    /**
     * Get dynamic SEO data for the career/job posting
     * This method is called by the SEO package to get custom SEO data
     */
    public function getDynamicSEOData(): SEOData
    {
        // Build JobPosting schema for Google Jobs compliance
        $jobPostingSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $this->title,
            'description' => $this->description ?? $this->short_description,
            'datePosted' => $this->published_at?->toIso8601String(),
            'validThrough' => $this->expires_at?->toIso8601String(),
        ];

        // Add employmentType from contract_type enum
        if ($this->contract_type) {
            $jobPostingSchema['employmentType'] = $this->contract_type->toSchemaOrg();
        }

        // Add jobLocation
        if ($this->location) {
            $jobPostingSchema['jobLocation'] = [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $this->location,
                    'addressCountry' => 'IR', // Iran
                ],
            ];
        }

        // Add hiringOrganization
        $jobPostingSchema['hiringOrganization'] = [
            '@type' => 'Organization',
            'name' => config('app.name', 'Hooshex'),
            'sameAs' => config('app.url', url('/')),
        ];

        // Add baseSalary if provided
        if ($this->salary_range) {
            $jobPostingSchema['baseSalary'] = [
                '@type' => 'MonetaryAmount',
                'currency' => 'IRR',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'value' => $this->salary_range,
                ],
            ];
        }

        // Add work type information
        if ($this->work_type) {
            $jobPostingSchema['jobLocationType'] = match($this->work_type) {
                WorkType::Remote => 'TELECOMMUTE',
                WorkType::OnSite => 'PLACE',
                WorkType::Hybrid => 'PLACE',
            };
        }

        // Add to schema collection
        $schemaCollection = SchemaCollection::initialize();
        $schemaCollection->push(new CustomSchema($jobPostingSchema));

        // Create SEOData with custom fields
        $seoData = new SEOData(
            title: $this->seo_title ?? $this->title,
            description: $this->seo_description ?? $this->short_description ?? strip_tags($this->description ?? ''),
            url: route('careers.show', $this->slug),
            schema: $schemaCollection,
            type: 'article',
            published_time: $this->published_at,
            modified_time: $this->updated_at,
        );

        return $seoData;
    }
}