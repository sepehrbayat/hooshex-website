<?php

declare(strict_types=1);

namespace App\Http\Livewire\AiTools;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Core\Models\Category;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Grid extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public array $pricing = [];

    #[Url(history: true)]
    public array $categories = [];

    public function render(): View
    {
        $query = AiTool::query()
            ->with('categories')
            ->when($this->pricing, fn ($q) => $q->whereIn('pricing_type', $this->pricing))
            ->when($this->categories, function ($q) {
                $q->whereHas('categories', fn ($cat) => $cat->whereIn('slug', $this->categories));
            })
            ->orderByDesc('published_at')
            ->orderBy('name');

        return view('livewire.ai-tools.grid', [
            'tools' => $query->paginate(12),
            'pricingOptions' => ['Free', 'Freemium', 'Paid'],
            'categoryOptions' => Category::where('type', 'ai_tool')->orderBy('name')->get(),
        ]);
    }
}

