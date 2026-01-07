<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'آیتم‌های سفارش';

    protected static ?string $label = 'آیتم';

    protected static ?string $pluralLabel = 'آیتم‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('orderable'))
            ->columns([
                Tables\Columns\TextColumn::make('orderable_type')
                    ->label('نوع')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '-')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('orderable_id')
                    ->label('شناسه')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('orderable_title')
                    ->label('عنوان')
                    ->getStateUsing(function ($record): string {
                        $orderable = $record->orderable;
                        if (!$orderable) {
                            return '-';
                        }

                        return (string) ($orderable->title
                            ?? $orderable->name
                            ?? $orderable->slug
                            ?? ('#' . $orderable->getKey()));
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('تعداد')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('قیمت')
                    ->money('IRR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('جمع')
                    ->getStateUsing(fn ($record) => (int) $record->price * (int) $record->quantity)
                    ->money('IRR')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('(price * quantity) ' . $direction);
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([])
            ->headerActions([])
            ->bulkActions([])
            ->defaultSort('id', 'asc');
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
