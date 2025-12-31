<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Force load thumbnail_id from database
        $record = $this->getRecord();
        $data['thumbnail_id'] = $record->thumbnail_id;
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // CuratorPicker returns array with media data, we need just the ID
        if (isset($data['thumbnail_id']) && is_array($data['thumbnail_id'])) {
            // Get the first item's ID
            $firstItem = reset($data['thumbnail_id']);
            if (is_array($firstItem) && isset($firstItem['id'])) {
                $data['thumbnail_id'] = $firstItem['id'];
            } elseif (isset($firstItem)) {
                $data['thumbnail_id'] = $firstItem;
            }
        }
        
        return $data;
    }
}

