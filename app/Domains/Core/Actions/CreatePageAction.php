<?php

declare(strict_types=1);

namespace App\Domains\Core\Actions;

use App\Domains\Core\Models\Page;

/**
 * Create Page Action
 * Handles creating static pages
 */
class CreatePageAction
{
    /**
     * Execute the action
     *
     * @param array $data Page data
     * @return Page
     */
    public function execute(array $data): Page
    {
        return Page::create([
            'title' => $data['title'],
            'slug' => $data['slug'] ?? \Str::slug($data['title']),
            'excerpt' => $data['excerpt'] ?? null,
            'content_blocks' => $data['content_blocks'] ?? [],
            'template' => $data['template'] ?? 'default',
            'is_published' => $data['is_published'] ?? false,
            'published_at' => $data['published_at'] ?? null,
        ]);
    }

    /**
     * Publish a page
     */
    public function publish(Page $page): Page
    {
        $page->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return $page;
    }

    /**
     * Unpublish a page
     */
    public function unpublish(Page $page): Page
    {
        $page->update([
            'is_published' => false,
        ]);

        return $page;
    }
}
