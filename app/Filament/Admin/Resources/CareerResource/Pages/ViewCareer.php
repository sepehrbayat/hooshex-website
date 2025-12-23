<?php

namespace App\Filament\Admin\Resources\CareerResource\Pages;

use App\Filament\Admin\Resources\CareerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCareer extends ViewRecord
{
    protected static string $resource = CareerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
