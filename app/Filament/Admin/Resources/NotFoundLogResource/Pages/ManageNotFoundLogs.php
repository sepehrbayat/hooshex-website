<?php

namespace App\Filament\Admin\Resources\NotFoundLogResource\Pages;

use App\Filament\Admin\Resources\NotFoundLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNotFoundLogs extends ManageRecords
{
    protected static string $resource = NotFoundLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 404 logs are created automatically by middleware
        ];
    }
}
