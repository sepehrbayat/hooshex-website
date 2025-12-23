<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;
}

