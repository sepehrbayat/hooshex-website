<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Core\Models\Page;
use App\Filament\Admin\Resources\PageResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use RalphJSmit\Filament\SEO\SEO;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('PageForm')
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
                            Forms\Components\Textarea::make('excerpt')
                                ->rows(3)
                                ->columnSpanFull(),
                            Forms\Components\Select::make('template')
                                ->options([
                                    'default' => 'Default',
                                    'home' => 'Home',
                                    'landing' => 'Landing Page',
                                    'contact' => 'Contact',
                                ])
                                ->default('default'),
                            Forms\Components\Toggle::make('is_published'),
                            Forms\Components\DateTimePicker::make('published_at'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Content Blocks')
                        ->schema([
                            Forms\Components\KeyValue::make('content_blocks')
                                ->keyLabel('Block Type')
                                ->valueLabel('Block Data')
                                ->helperText('Structured JSON data for page blocks. Use key-value pairs where key is block type (e.g., "hero", "features") and value is JSON string.')
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
                Tables\Columns\TextColumn::make('template')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('template')
                    ->options([
                        'default' => 'Default',
                        'home' => 'Home',
                        'landing' => 'Landing Page',
                        'contact' => 'Contact',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published'),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}