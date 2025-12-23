<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $aiCategories = ['Chat', 'Image', 'Productivity', 'Coding'];
        foreach ($aiCategories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name), 'type' => 'ai_tool'],
                ['name' => $name]
            );
        }

        $postCategories = ['News', 'Guides', 'Releases'];
        foreach ($postCategories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name), 'type' => 'post'],
                ['name' => $name]
            );
        }
    }
}

