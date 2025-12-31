<?php

declare(strict_types=1);

namespace App\Livewire\Common;

use Livewire\Component;
use Livewire\WithPagination;

/**
 * Generic Search component with debounce and filtering
 */
abstract class SearchableList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'latest';
    public int $perPage = 12;
    public array $filters = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    /**
     * Reset pagination when search changes
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when sort changes
     */
    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when any filter changes
     */
    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    /**
     * Clear all filters
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->sortBy = 'latest';
        $this->filters = [];
        $this->resetPage();
    }

    /**
     * Set a specific filter
     */
    public function setFilter(string $key, mixed $value): void
    {
        $this->filters[$key] = $value;
        $this->resetPage();
    }

    /**
     * Remove a specific filter
     */
    public function removeFilter(string $key): void
    {
        unset($this->filters[$key]);
        $this->resetPage();
    }

    /**
     * Get the query for fetching items - to be implemented by child classes
     */
    abstract protected function getItems();

    /**
     * Get available sort options - to be overridden by child classes
     */
    protected function getSortOptions(): array
    {
        return [
            'latest' => 'جدیدترین',
            'popular' => 'محبوب‌ترین',
        ];
    }
}
