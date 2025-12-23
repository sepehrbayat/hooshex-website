<?php

declare(strict_types=1);

namespace App\Http\Controllers\Blog;

use App\Domains\Blog\Models\Post;
use App\Http\Controllers\Controller;
use App\Services\Content\TocGenerator;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly TocGenerator $tocGenerator
    ) {
    }

    /**
     * Display the specified post.
     */
    public function show(string $slug): View
    {
        $post = Post::where('slug', $slug)
            ->where('status', \App\Enums\PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['author', 'primaryCategory', 'thumbnail', 'categories'])
            ->firstOrFail();

        // Generate TOC and parse content
        $parsed = $this->tocGenerator->parse($post->content ?? '');

        return view('posts.show', [
            'post' => $post,
            'parsedContent' => $parsed['html'],
            'toc' => $parsed['toc'],
        ]);
    }
}
