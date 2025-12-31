<?php

declare(strict_types=1);

namespace App\Livewire\Common;

use Livewire\Component;

/**
 * Toast notification component
 */
class ToastNotification extends Component
{
    public array $toasts = [];

    protected $listeners = [
        'show-toast' => 'addToast',
    ];

    public function addToast(array $data): void
    {
        $id = uniqid();
        
        $this->toasts[$id] = [
            'id' => $id,
            'message' => $data['message'] ?? '',
            'type' => $data['type'] ?? 'info', // success, error, warning, info
            'duration' => $data['duration'] ?? 5000,
        ];

        // Auto-remove after duration
        $this->dispatch('remove-toast', id: $id, delay: $data['duration'] ?? 5000);
    }

    public function removeToast(string $id): void
    {
        unset($this->toasts[$id]);
    }

    public function render()
    {
        return view('livewire.common.toast-notification');
    }
}
