<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Core\Models\Category;
use App\Filament\Admin\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Admin\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Admin\Resources\CategoryResource\Pages\ListCategories;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'دسته‌بندی‌ها';

    protected static ?string $modelLabel = 'دسته‌بندی';

    protected static ?string $pluralModelLabel = 'دسته‌بندی‌ها';

    protected static ?string $navigationGroup = 'محتوا';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        if ($state === null || $state === '') {
                            return;
                        }

                        if (!empty($get('slug'))) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),

                Forms\Components\TextInput::make('slug')
                    ->label('نامک یکتا (Slug)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash()
                    ->maxLength(255)
                    ->prefix('/'),

                Forms\Components\Select::make('type')
                    ->label('نوع')
                    ->required()
                    ->options([
                        'ai_tool' => 'ابزارهای هوش مصنوعی',
                        'post' => 'مقالات',
                        'news' => 'اخبار',
                        'product' => 'محصولات',
                    ])
                    ->native(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('نوع')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'ai_tool' => 'ابزارهای هوش مصنوعی',
                        'post' => 'مقالات',
                        'news' => 'اخبار',
                        'product' => 'محصولات',
                        default => $state ?? '-',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخرین ویرایش')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع')
                    ->options([
                        'ai_tool' => 'ابزارهای هوش مصنوعی',
                        'post' => 'مقالات',
                        'news' => 'اخبار',
                        'product' => 'محصولات',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
