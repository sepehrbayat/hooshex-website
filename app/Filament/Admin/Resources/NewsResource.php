<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Blog\Models\News;
use App\Domains\Core\Models\Category;
use App\Filament\Admin\Resources\NewsResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use RalphJSmit\Filament\SEO\SEO;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('NewsForm')
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
                            Forms\Components\Select::make('author_id')
                                ->relationship('author', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'scheduled' => 'Scheduled',
                                ])
                                ->required()
                                ->default('draft'),
                            Forms\Components\DateTimePicker::make('published_at')
                                ->helperText('For scheduled posts, set a future date'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Content')
                        ->schema([
                            Forms\Components\Textarea::make('excerpt')
                                ->rows(3)
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('content')
                                ->columnSpanFull(),
                            CuratorPicker::make('thumbnail_id')
                                ->label('تصویر شاخص')
                                ->directory('news')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Taxonomy')
                        ->schema([
                            Forms\Components\Select::make('categories')
                                ->multiple()
                                ->relationship('categories', 'name', fn ($query) => $query->where('type', 'news'))
                                ->options(Category::where('type', 'news')->pluck('name', 'id'))
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
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'scheduled' => 'Scheduled',
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}