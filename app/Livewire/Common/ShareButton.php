<?php

declare(strict_types=1);

namespace App\Livewire\Common;

use Livewire\Component;

/**
 * Share button component with social media options
 */
class ShareButton extends Component
{
    public string $url;
    public string $title;
    public ?string $description = null;
    public bool $showDropdown = false;

    public function mount(string $url, string $title, ?string $description = null): void
    {
        $this->url = $url;
        $this->title = $title;
        $this->description = $description;
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function copyLink(): void
    {
        $this->dispatch('copy-to-clipboard', url: $this->url);
        $this->dispatch('show-toast', [
            'message' => 'لینک کپی شد!',
            'type' => 'success',
        ]);
        $this->showDropdown = false;
    }

    public function getShareLinks(): array
    {
        $encodedUrl = urlencode($this->url);
        $encodedTitle = urlencode($this->title);
        $encodedDescription = urlencode($this->description ?? '');

        return [
            'telegram' => "https://t.me/share/url?url={$encodedUrl}&text={$encodedTitle}",
            'whatsapp' => "https://api.whatsapp.com/send?text={$encodedTitle}%20{$encodedUrl}",
            'twitter' => "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}",
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}",
        ];
    }

    public function render()
    {
        return view('livewire.common.share-button', [
            'shareLinks' => $this->getShareLinks(),
        ]);
    }
}
