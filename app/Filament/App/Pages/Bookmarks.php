<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Domains\AiTools\Models\AiTool;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class Bookmarks extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static string $view = 'filament.app.pages.bookmarks';

    protected static ?string $navigationLabel = 'نشان‌گذاری‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AiTool::query()
                    ->whereIn('id', function ($query) {
                        $query->select('ai_tool_id')
                            ->from('bookmarks')
                            ->where('user_id', auth()->id());
                    })
            )
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('لوگو')
                    ->defaultImageUrl(url('/images/placeholder.png')),
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pricing_type')
                    ->label('نوع قیمت')
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\Action::make('remove')
                    ->label('حذف')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(function (AiTool $record) {
                        DB::table('bookmarks')
                            ->where('user_id', auth()->id())
                            ->where('ai_tool_id', $record->id)
                            ->delete();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('نشان‌گذاری حذف شد')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name');
    }
}