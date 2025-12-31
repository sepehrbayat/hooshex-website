<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AiToolResource\Pages;

use App\Filament\Admin\Resources\AiToolResource;
use Filament\Resources\Pages\ListRecords;

class ListAiTools extends ListRecords
{
    protected static string $resource = AiToolResource::class;

    /**
     * Modify the table query to optimize performance
     * Uses withCount to efficiently count clicks without N+1 queries
     */
    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getTableQuery()
            ->withCount('clicks');
    }
}

