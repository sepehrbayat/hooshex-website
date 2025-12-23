<?php

namespace App\Filament\Admin\Resources\NavigationItemResource\Pages;

use App\Filament\Admin\Resources\NavigationItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNavigationItems extends ListRecords
{
    protected static string $resource = NavigationItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
