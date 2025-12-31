<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $group = 'seo';
        $defaults = [
            'title_separator' => ' | ',
            'title_suffix' => null,
            'noindex_tags' => false,
            'noindex_categories' => false,
            'noindex_search' => false,
            'default_schema_ai_tools' => 'SoftwareApplication',
            'default_schema_posts' => 'Article',
            'default_schema_courses' => 'Course',
            'default_schema_products' => 'Product',
            'include_ai_tools_in_sitemap' => true,
            'include_posts_in_sitemap' => true,
            'include_news_in_sitemap' => true,
            'include_courses_in_sitemap' => true,
            'include_products_in_sitemap' => true,
            'include_teachers_in_sitemap' => true,
            'include_pages_in_sitemap' => true,
            'include_careers_in_sitemap' => true,
        ];

        foreach ($defaults as $name => $value) {
            // Encode value as JSON payload (Spatie Laravel Settings format)
            // For null values, use empty string for strings, empty array for arrays
            if ($value === null) {
                $payload = json_encode('');
            } else {
                $payload = json_encode($value);
            }
            
            DB::table('settings')->updateOrInsert(
                [
                    'name' => $name,
                    'group' => $group,
                ],
                [
                    'payload' => $payload,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

