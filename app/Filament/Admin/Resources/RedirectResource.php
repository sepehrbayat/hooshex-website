<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Core\Models\Redirect;
use App\Filament\Admin\Resources\RedirectResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'تغییر مسیرها';

    protected static ?string $modelLabel = 'تغییر مسیر';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('source_url')
                    ->label('URL مبدأ')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(500)
                    ->helperText('مثال: /old-page یا /old-page/'),
                Forms\Components\TextInput::make('target_url')
                    ->label('URL مقصد')
                    ->required()
                    ->maxLength(500)
                    ->helperText('مثال: /new-page یا https://example.com'),
                Forms\Components\Select::make('status_code')
                    ->label('کد وضعیت')
                    ->options([
                        301 => '301 - Permanent Redirect',
                        302 => '302 - Temporary Redirect',
                        307 => '307 - Temporary Redirect (Preserve Method)',
                    ])
                    ->default(301)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('source_url')
                    ->label('URL مبدأ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_url')
                    ->label('URL مقصد')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status_code')
                    ->label('کد وضعیت')
                    ->colors([
                        'primary' => 301,
                        'warning' => 302,
                        'success' => 307,
                    ]),
                Tables\Columns\TextColumn::make('hit_count')
                    ->label('تعداد بازدید')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('last_accessed_at')
                    ->label('آخرین بازدید')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_code')
                    ->label('کد وضعیت')
                    ->options([
                        301 => '301',
                        302 => '302',
                        307 => '307',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('hit_count', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit' => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'SEO';
    }
}