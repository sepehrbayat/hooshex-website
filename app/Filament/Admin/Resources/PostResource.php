<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Blog\Models\Post;
use App\Domains\Core\Models\Category;
use App\Enums\PostStatus;
use App\Enums\PostType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use RalphJSmit\Filament\SEO\SEO;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Split::make([
                // Main Content Area (Left)
                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                if (empty($get('slug'))) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash(),
                        Forms\Components\RichEditor::make('content')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'h2',
                                'h3',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'codeBlock',
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('excerpt')
                            ->rows(3)
                            ->helperText('Auto-generated if left empty')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 2]),

                // Sidebar Area (Right)
                Forms\Components\Section::make('Settings')
                    ->schema([
                        CuratorPicker::make('thumbnail_id')
                            ->label('تصویر شاخص')
                            ->directory('thumbnails')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                        Forms\Components\Select::make('author_id')
                            ->label('Author')
                            ->relationship('author', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('primary_category_id')
                            ->label('Primary Category')
                            ->relationship('primaryCategory', 'name', fn ($query) => $query->where('type', 'post'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('type')
                            ->options([
                                PostType::Article->value => 'Article',
                                PostType::News->value => 'News',
                            ])
                            ->required()
                            ->default(PostType::Article->value),
                        Forms\Components\Select::make('status')
                            ->options([
                                PostStatus::Draft->value => 'Draft',
                                PostStatus::Published->value => 'Published',
                                PostStatus::Scheduled->value => 'Scheduled',
                            ])
                            ->required()
                            ->default(PostStatus::Draft->value),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->helperText('For scheduled posts, set a future date'),
                        Forms\Components\Placeholder::make('reading_time')
                            ->label('Reading Time')
                            ->content(fn ($record) => $record?->reading_time 
                                ? $record->reading_time . ' minutes' 
                                : 'Calculated automatically on save'),
                        Forms\Components\Select::make('categories')
                            ->label('Categories')
                            ->multiple()
                            ->relationship('categories', 'name', fn ($query) => $query->where('type', 'post'))
                            ->preload(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])->from('lg'),

            // SEO Section (Full Width)
            Forms\Components\Section::make('SEO')
                ->schema([
                    SEO::make(),
                ])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->value ?? 'N/A'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->value ?? 'N/A'),
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        PostType::Article->value => 'Article',
                        PostType::News->value => 'News',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        PostStatus::Draft->value => 'Draft',
                        PostStatus::Published->value => 'Published',
                        PostStatus::Scheduled->value => 'Scheduled',
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
            'index' => PostResource\Pages\ListPosts::route('/'),
            'create' => PostResource\Pages\CreatePost::route('/create'),
            'edit' => PostResource\Pages\EditPost::route('/{record}/edit'),
        ];
    }
}