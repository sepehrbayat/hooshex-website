<?php

declare(strict_types=1);

namespace App\Observers;

use App\Domains\Blog\Models\Post;

class PostObserver
{
    /**
     * Handle the Post "saving" event.
     */
    public function saving(Post $post): void
    {
        // Calculate reading time
        if ($post->isDirty('content') && !empty($post->content)) {
            $plainText = strip_tags($post->content);
            $wordCount = str_word_count($plainText);
            $readingTime = (int) ceil($wordCount / 200); // Average reading speed: 200 words per minute
            $post->reading_time = $readingTime > 0 ? $readingTime : 1; // Minimum 1 minute
        }

        // Auto-generate excerpt if empty
        if (empty($post->excerpt) && !empty($post->content)) {
            $plainText = strip_tags($post->content);
            $excerpt = mb_substr($plainText, 0, 160, 'UTF-8');
            if (mb_strlen($plainText, 'UTF-8') > 160) {
                $excerpt .= '...';
            }
            $post->excerpt = trim($excerpt);
        }
    }
}

