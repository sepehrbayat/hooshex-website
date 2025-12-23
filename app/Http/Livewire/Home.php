<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\ViewModels\HomePageData;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Home extends Component
{
    public string $search = '';

    /**
     * Render the component
     */
    public function render(): View
    {
        return view('livewire.home', [
            'features' => HomePageData::features(),
            'testimonials' => HomePageData::testimonials(),
            'blogs' => HomePageData::blogs(),
            'courses' => HomePageData::courses(),
        ])->layout('components.layouts.app');
    }
}
