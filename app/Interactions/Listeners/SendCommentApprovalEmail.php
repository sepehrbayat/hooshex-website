<?php

declare(strict_types=1);

namespace App\Interactions\Listeners;

use App\Interactions\Events\CommentApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Send Comment Approval Email Listener
 * Notifies user when their comment is approved
 */
class SendCommentApprovalEmail implements ShouldQueue
{
    /**
     * Handle the event
     */
    public function handle(CommentApproved $event): void
    {
        $comment = $event->comment;

        Log::info('Comment approved', [
            'comment_id' => $comment->id,
            'user_id' => $comment->user_id,
        ]);

        // TODO: Send email notification to user
        // $comment->user->notify(new CommentApprovedNotification($comment));
    }
}
