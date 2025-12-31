<?php

declare(strict_types=1);

namespace App\Domains\AiTools\Actions;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Auth\Models\User;

/**
 * Bookmark AI Tool Action
 * Handles adding/removing AI tools from user bookmarks
 */
class BookmarkAiToolAction
{
    /**
     * Toggle bookmark status for an AI tool
     *
     * @param User $user The user
     * @param AiTool $aiTool The AI tool to bookmark/unbookmark
     * @return array{bookmarked: bool, message: string}
     */
    public function execute(User $user, AiTool $aiTool): array
    {
        $isBookmarked = $user->bookmarkedAiTools()->where('ai_tool_id', $aiTool->id)->exists();

        if ($isBookmarked) {
            $user->bookmarkedAiTools()->detach($aiTool->id);
            return [
                'bookmarked' => false,
                'message' => 'ابزار از نشان‌گذاری‌ها حذف شد',
            ];
        }

        $user->bookmarkedAiTools()->attach($aiTool->id);
        return [
            'bookmarked' => true,
            'message' => 'ابزار به نشان‌گذاری‌ها اضافه شد',
        ];
    }

    /**
     * Add bookmark
     */
    public function add(User $user, AiTool $aiTool): bool
    {
        if (!$user->bookmarkedAiTools()->where('ai_tool_id', $aiTool->id)->exists()) {
            $user->bookmarkedAiTools()->attach($aiTool->id);
            return true;
        }
        return false;
    }

    /**
     * Remove bookmark
     */
    public function remove(User $user, AiTool $aiTool): bool
    {
        return $user->bookmarkedAiTools()->detach($aiTool->id) > 0;
    }
}
