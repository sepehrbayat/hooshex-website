<?php

declare(strict_types=1);

namespace App\Livewire\Common;

use Livewire\Component;

/**
 * Modal component for reusable modal dialogs
 */
class Modal extends Component
{
    public bool $show = false;
    public string $maxWidth = 'md';
    public bool $closeable = true;

    protected $listeners = [
        'open-modal' => 'open',
        'close-modal' => 'close',
    ];

    public function open(string $modalId = null): void
    {
        // If a specific modal is targeted
        if ($modalId && $modalId !== $this->getId()) {
            return;
        }
        
        $this->show = true;
    }

    public function close(string $modalId = null): void
    {
        if ($modalId && $modalId !== $this->getId()) {
            return;
        }

        if ($this->closeable) {
            $this->show = false;
        }
    }

    public function getMaxWidthClass(): string
    {
        return match ($this->maxWidth) {
            'sm' => 'sm:max-w-sm',
            'md' => 'sm:max-w-md',
            'lg' => 'sm:max-w-lg',
            'xl' => 'sm:max-w-xl',
            '2xl' => 'sm:max-w-2xl',
            '3xl' => 'sm:max-w-3xl',
            '4xl' => 'sm:max-w-4xl',
            '5xl' => 'sm:max-w-5xl',
            'full' => 'sm:max-w-full',
            default => 'sm:max-w-md',
        };
    }

    public function render()
    {
        return view('livewire.common.modal');
    }
}
