<?php

declare(strict_types=1);

namespace App\Livewire\Common;

use Livewire\Component;

/**
 * Bookmark/Favorite button component
 */
class BookmarkButton extends Component
{
    public int $itemId;
    public string $itemType;
    public bool $isBookmarked = false;
    public int $bookmarkCount = 0;

    public function mount(int $itemId, string $itemType, bool $isBookmarked = false, int $bookmarkCount = 0): void
    {
        $this->itemId = $itemId;
        $this->itemType = $itemType;
        $this->isBookmarked = $isBookmarked;
        $this->bookmarkCount = $bookmarkCount;
    }

    public function toggle(): void
    {
        if (!auth()->check()) {
            $this->dispatch('open-login-modal');
            return;
        }

        $user = auth()->user();

        // Use polymorphic bookmarks table
        $bookmark = $user->bookmarks()
            ->where('bookmarkable_type', $this->itemType)
            ->where('bookmarkable_id', $this->itemId)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $this->isBookmarked = false;
            $this->bookmarkCount = max(0, $this->bookmarkCount - 1);
        } else {
            $user->bookmarks()->create([
                'bookmarkable_type' => $this->itemType,
                'bookmarkable_id' => $this->itemId,
            ]);
            $this->isBookmarked = true;
            $this->bookmarkCount++;
        }

        $this->dispatch('bookmark-toggled', [
            'itemId' => $this->itemId,
            'itemType' => $this->itemType,
            'isBookmarked' => $this->isBookmarked,
        ]);
    }

    public function render()
    {
        return view('livewire.common.bookmark-button');
    }
}
