<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Core\Models\NavigationItem;
use App\Filament\Admin\Resources\NavigationItemResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NavigationItemResource extends Resource
{
    protected static ?string $model = NavigationItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $navigationLabel = 'منوی ناوبری';

    protected static ?string $modelLabel = 'آیتم منو';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('menu_location')
                    ->label('موقعیت منو')
                    ->options([
                        'header' => 'هدر',
                        'footer' => 'فوتر',
                    ])
                    ->required()
                    ->default('header'),
                Forms\Components\TextInput::make('label')
                    ->label('برچسب')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('route')
                    ->label('Route')
                    ->options([
                        'home' => 'خانه',
                        'ai-tools.index' => 'ابزارهای AI',
                    ])
                    ->searchable(),
                Forms\Components\TextInput::make('url')
                    ->label('URL')
                    ->url()
                    ->maxLength(500)
                    ->helperText('اگر route انتخاب نشده باشد، از این URL استفاده می‌شود'),
                Forms\Components\TextInput::make('icon')
                    ->label('آیکون')
                    ->maxLength(50)
                    ->helperText('نام آیکون Heroicon'),
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'label')
                    ->label('منوی والد')
                    ->searchable(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
                Forms\Components\Toggle::make('open_in_new_tab')
                    ->label('باز شدن در تب جدید')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('menu_location')
                    ->label('موقعیت')
                    ->badge()
                    ->colors([
                        'primary' => 'header',
                        'gray' => 'footer',
                    ]),
                Tables\Columns\TextColumn::make('label')
                    ->label('برچسب')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('route')
                    ->label('Route'),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->limit(30),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('menu_location')
                    ->options([
                        'header' => 'هدر',
                        'footer' => 'فوتر',
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
            ->defaultSort('menu_location')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNavigationItems::route('/'),
            'create' => Pages\CreateNavigationItem::route('/create'),
            'edit' => Pages\EditNavigationItem::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'تنظیمات';
    }
}