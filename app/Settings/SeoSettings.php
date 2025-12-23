<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SeoSettings extends Settings
{
    public string $title_separator;
    public ?string $title_suffix;
    public bool $noindex_tags;
    public bool $noindex_categories;
    public bool $noindex_search;
    public ?string $default_schema_ai_tools;
    public ?string $default_schema_posts;
    public ?string $default_schema_courses;
    public ?string $default_schema_products;
    public bool $include_ai_tools_in_sitemap;
    public bool $include_posts_in_sitemap;
    public bool $include_news_in_sitemap;
    public bool $include_courses_in_sitemap;
    public bool $include_products_in_sitemap;
    public bool $include_teachers_in_sitemap;
    public bool $include_pages_in_sitemap;
    public bool $include_careers_in_sitemap;

    public static function group(): string
    {
        return 'seo';
    }
}