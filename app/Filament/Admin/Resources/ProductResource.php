<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Commerce\Models\Product;
use App\Domains\Core\Models\Category;
use App\Filament\Admin\Resources\ProductResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use RalphJSmit\Filament\SEO\SEO;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('ProductForm')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('short_description')
                                ->rows(3)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('sku')
                                ->maxLength(100)
                                ->unique(ignoreRecord: true),
                            Forms\Components\Toggle::make('is_digital')
                                ->label('Digital Product'),
                            Forms\Components\Toggle::make('is_featured'),
                            Forms\Components\DateTimePicker::make('published_at'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Content')
                        ->schema([
                            Forms\Components\RichEditor::make('description')
                                ->columnSpanFull(),
                            CuratorPicker::make('thumbnail_id')
                                ->label('تصویر شاخص')
                                ->directory('products')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('file_url')
                                ->url()
                                ->maxLength(500)
                                ->visible(fn (Forms\Get $get) => $get('is_digital'))
                                ->helperText('Download URL for digital products'),
                        ]),

                    Forms\Components\Tabs\Tab::make('Pricing & Stock')
                        ->schema([
                            Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->required()
                                ->default(0)
                                ->helperText('Price in Toman'),
                            Forms\Components\TextInput::make('sale_price')
                                ->numeric()
                                ->nullable()
                                ->helperText('Sale price in Toman'),
                            Forms\Components\Select::make('stock_status')
                                ->options([
                                    'in_stock' => 'In Stock',
                                    'out_of_stock' => 'Out of Stock',
                                    'on_backorder' => 'On Backorder',
                                ])
                                ->required()
                                ->default('in_stock'),
                            Forms\Components\TextInput::make('stock_quantity')
                                ->numeric()
                                ->nullable()
                                ->helperText('Leave empty for unlimited'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Taxonomy')
                        ->schema([
                            Forms\Components\Select::make('categories')
                                ->multiple()
                                ->relationship('categories', 'name', fn ($query) => $query->where('type', 'product'))
                                ->options(Category::where('type', 'product')->pluck('name', 'id'))
                                ->preload()
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('SEO')
                        ->schema([
                            SEO::make(),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IRR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_digital')
                    ->boolean(),
                Tables\Columns\TextColumn::make('stock_status')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_digital'),
                Tables\Filters\TernaryFilter::make('is_featured'),
                Tables\Filters\SelectFilter::make('stock_status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'out_of_stock' => 'Out of Stock',
                        'on_backorder' => 'On Backorder',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}