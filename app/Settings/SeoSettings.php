<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SeoSettings extends Settings
{
    public string $title_separator = ' | ';
    public ?string $title_suffix = null;
    public bool $noindex_tags = false;
    public bool $noindex_categories = false;
    public bool $noindex_search = false;
    public ?string $default_schema_ai_tools = 'SoftwareApplication';
    public ?string $default_schema_posts = 'Article';
    public ?string $default_schema_courses = 'Course';
    public ?string $default_schema_products = 'Product';
    public bool $include_ai_tools_in_sitemap = true;
    public bool $include_posts_in_sitemap = true;
    public bool $include_news_in_sitemap = true;
    public bool $include_courses_in_sitemap = true;
    public bool $include_products_in_sitemap = true;
    public bool $include_teachers_in_sitemap = true;
    public bool $include_pages_in_sitemap = true;
    public bool $include_careers_in_sitemap = true;

    public static function group(): string
    {
        return 'seo';
    }
}