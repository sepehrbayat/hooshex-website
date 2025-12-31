<?php

declare(strict_types=1);

namespace App\Interactions\Actions;

use App\Interactions\Comment;
use App\Interactions\Events\CommentApproved;

/**
 * Approve Comment Action
 * Handles approving a comment
 */
class ApproveCommentAction
{
    /**
     * Execute the action
     *
     * @param Comment $comment The comment to approve
     * @return Comment
     */
    public function execute(Comment $comment): Comment
    {
        $comment->update(['status' => 'approved']);

        event(new CommentApproved($comment));

        return $comment;
    }

    /**
     * Mark comment as spam
     */
    public function markAsSpam(Comment $comment): Comment
    {
        $comment->update(['status' => 'spam']);
        return $comment;
    }

    /**
     * Trash a comment
     */
    public function trash(Comment $comment): Comment
    {
        $comment->update(['status' => 'trash']);
        return $comment;
    }
}
