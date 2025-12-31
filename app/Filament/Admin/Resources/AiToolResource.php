<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Core\Models\Category;
use App\Enums\PricingType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use RalphJSmit\Filament\SEO\SEO;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class AiToolResource extends Resource
{
    protected static ?string $model = AiTool::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('AiToolForm')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(200)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('short_description')
                                ->rows(3)
                                ->maxLength(160)
                                ->helperText('Maximum 160 characters for SEO')
                                ->columnSpanFull(),
                            CuratorPicker::make('logo_id')
                                ->label('لوگو')
                                ->directory('logos')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                                ->columnSpanFull(),
                            Forms\Components\Select::make('pricing_type')
                                ->options([
                                    PricingType::Free->value => 'Free',
                                    PricingType::Freemium->value => 'Freemium',
                                    PricingType::Paid->value => 'Paid',
                                    PricingType::FreeTrial->value => 'Free Trial',
                                    PricingType::Contact->value => 'Contact',
                                ])
                                ->nullable(),
                            Forms\Components\Toggle::make('is_verified')
                                ->label('Verified'),
                            Forms\Components\DateTimePicker::make('published_at'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Links')
                        ->schema([
                            Forms\Components\TextInput::make('website_url')
                                ->url()
                                ->maxLength(500)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('affiliate_url')
                                ->url()
                                ->maxLength(500)
                                ->label('Affiliate URL (Money Link)')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('deal_url')
                                ->url()
                                ->maxLength(500)
                                ->label('Deal URL (Coupon/Special Offer)')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('demo_url')
                                ->url()
                                ->maxLength(500)
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Content')
                        ->schema([
                            Forms\Components\RichEditor::make('content')
                                ->label('Description')
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('features')
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('icon')
                                        ->maxLength(100)
                                        ->helperText('Icon class or identifier'),
                                ])
                                ->columns(2)
                                ->columnSpanFull(),
                            Forms\Components\TagsInput::make('pros')
                                ->label('Pros')
                                ->placeholder('Add a pro and press Enter')
                                ->columnSpanFull(),
                            Forms\Components\TagsInput::make('cons')
                                ->label('Cons')
                                ->placeholder('Add a con and press Enter')
                                ->columnSpanFull(),
                            Forms\Components\TagsInput::make('languages')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('rating')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(5)
                                ->step(0.1),
                            Forms\Components\TextInput::make('users_count')
                                ->numeric()
                                ->minValue(0),
                            Forms\Components\TextInput::make('success_rate')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->suffix('%'),
                            Forms\Components\TextInput::make('response_time')
                                ->maxLength(50),
                            Forms\Components\TextInput::make('company')
                                ->maxLength(255),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Gallery')
                        ->schema([
                            CuratorPicker::make('gallery_ids')
                                ->label('Gallery Images')
                                ->multiple()
                                ->directory('ai-tools/gallery')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Taxonomy')
                        ->schema([
                            Forms\Components\Select::make('categories')
                                ->multiple()
                                ->relationship('categories', 'name', fn ($query) => $query->where('type', 'ai_tool'))
                                ->options(Category::where('type', 'ai_tool')->pluck('name', 'id'))
                                ->preload()
                                ->columnSpanFull(),
                            Forms\Components\Select::make('related_course_id')
                                ->relationship('relatedCourse', 'title')
                                ->searchable()
                                ->preload()
                                ->columnSpanFull(),
                            Forms\Components\Toggle::make('has_course')
                                ->label('Has Related Course'),
                            Forms\Components\Toggle::make('is_featured'),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pricing_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->value ?? 'N/A'),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('rating')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) : 'N/A'),
                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('تعداد کلیک')
                    ->counts('clicks')
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('pricing_type')
                    ->options([
                        PricingType::Free->value => 'Free',
                        PricingType::Freemium->value => 'Freemium',
                        PricingType::Paid->value => 'Paid',
                        PricingType::FreeTrial->value => 'Free Trial',
                        PricingType::Contact->value => 'Contact',
                    ]),
                Tables\Filters\TernaryFilter::make('is_verified'),
                Tables\Filters\TernaryFilter::make('is_featured'),
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
            'index' => AiToolResource\Pages\ListAiTools::route('/'),
            'create' => AiToolResource\Pages\CreateAiTool::route('/create'),
            'edit' => AiToolResource\Pages\EditAiTool::route('/{record}/edit'),
        ];
    }
}