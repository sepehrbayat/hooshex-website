<?php

declare(strict_types=1);

namespace App\Interactions\Actions;

use App\Domains\Auth\Models\User;
use App\Interactions\Comment;
use App\Interactions\Events\CommentCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Create Comment Action
 * Handles creating a new comment
 */
class CreateCommentAction
{
    /**
     * Execute the action
     *
     * @param Model $commentable The model being commented on (Post, Lesson, etc.)
     * @param User $user The user creating the comment
     * @param string $body The comment body
     * @param int|null $parentId Parent comment ID for replies
     * @param Request|null $request The HTTP request for IP/UA tracking
     * @return Comment
     */
    public function execute(
        Model $commentable,
        User $user,
        string $body,
        ?int $parentId = null,
        ?Request $request = null
    ): Comment {
        $comment = new Comment([
            'user_id' => $user->id,
            'body' => $body,
            'parent_id' => $parentId,
            'status' => 'pending', // Requires moderation
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);

        $commentable->comments()->save($comment);

        event(new CommentCreated($comment));

        return $comment;
    }
}
