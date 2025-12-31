<?php

declare(strict_types=1);

namespace App\Interactions\Listeners;

use App\Interactions\Events\CommentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Notify Admin New Comment Listener
 * Sends notification to admin when new comment is submitted
 */
class NotifyAdminNewComment implements ShouldQueue
{
    /**
     * Handle the event
     */
    public function handle(CommentCreated $event): void
    {
        $comment = $event->comment;

        // Log the new comment for now
        Log::info('New comment submitted', [
            'comment_id' => $comment->id,
            'user_id' => $comment->user_id,
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id,
            'body_preview' => \Str::limit($comment->body, 100),
        ]);

        // TODO: Implement actual notification
        // Notification::route('mail', config('mail.admin_email'))
        //     ->notify(new NewCommentNotification($comment));
    }
}
