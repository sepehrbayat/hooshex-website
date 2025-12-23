<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Core\Models\Career;
use App\Enums\WorkType;
use App\Enums\ContractType;
use App\Filament\Admin\Resources\CareerResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use RalphJSmit\Filament\SEO\SEO;

class CareerResource extends Resource
{
    protected static ?string $model = Career::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('CareerForm')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')
                        ->schema([
                            // Header Section
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                    if (empty($get('slug'))) {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                })
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->alphaDash()
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('department')
                                ->label('Department')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('short_description')
                                ->label('Short Description')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Brief summary for listings and SEO')
                                ->columnSpanFull(),

                            // Job Details Grid
                            Forms\Components\Select::make('work_type')
                                ->label('Work Type')
                                ->options([
                                    WorkType::Remote->value => WorkType::Remote->label(),
                                    WorkType::OnSite->value => WorkType::OnSite->label(),
                                    WorkType::Hybrid->value => WorkType::Hybrid->label(),
                                ])
                                ->required()
                                ->default(WorkType::Remote->value),
                            Forms\Components\Select::make('contract_type')
                                ->label('Contract Type')
                                ->options([
                                    ContractType::FullTime->value => ContractType::FullTime->label(),
                                    ContractType::PartTime->value => ContractType::PartTime->label(),
                                    ContractType::Contract->value => ContractType::Contract->label(),
                                    ContractType::Internship->value => ContractType::Internship->label(),
                                ])
                                ->required()
                                ->default(ContractType::FullTime->value),
                            Forms\Components\TextInput::make('location')
                                ->label('Location')
                                ->maxLength(255)
                                ->helperText('e.g., تهران'),
                            Forms\Components\TextInput::make('experience_level')
                                ->label('Experience Level')
                                ->maxLength(100)
                                ->helperText('e.g., Senior, Junior, Mid-level'),
                            Forms\Components\TextInput::make('salary_range')
                                ->label('Salary Range')
                                ->maxLength(255)
                                ->helperText('e.g., "Negotiable" or "15-20M"'),
                            Forms\Components\TextInput::make('application_link')
                                ->label('Application Link')
                                ->url()
                                ->maxLength(500)
                                ->helperText('Link to Google Form or mailto: email'),
                            Forms\Components\DateTimePicker::make('published_at')
                                ->label('Published At')
                                ->helperText('When to publish this job posting'),
                            Forms\Components\DateTimePicker::make('expires_at')
                                ->label('Expires At')
                                ->helperText('Optional: When this job posting expires (required for Google Jobs)'),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->helperText('Inactive jobs won\'t appear on the website'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Description')
                        ->schema([
                            Forms\Components\RichEditor::make('description')
                                ->label('Job Description')
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
                                ])
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('responsibilities')
                                ->label('Responsibilities')
                                ->schema([
                                    Forms\Components\TextInput::make('item')
                                        ->label('Responsibility')
                                        ->required()
                                        ->maxLength(500),
                                ])
                                ->defaultItems(0)
                                ->addActionLabel('Add Responsibility')
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['item'] ?? null)
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('requirements')
                                ->label('Requirements')
                                ->schema([
                                    Forms\Components\TextInput::make('item')
                                        ->label('Requirement')
                                        ->required()
                                        ->maxLength(500),
                                ])
                                ->defaultItems(0)
                                ->addActionLabel('Add Requirement')
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['item'] ?? null)
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('benefits')
                                ->label('Benefits')
                                ->schema([
                                    Forms\Components\TextInput::make('item')
                                        ->label('Benefit')
                                        ->required()
                                        ->maxLength(500),
                                ])
                                ->defaultItems(0)
                                ->addActionLabel('Add Benefit')
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['item'] ?? null)
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
                Tables\Columns\TextColumn::make('department')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('work_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? '-')
                    ->color(fn ($state) => match($state) {
                        WorkType::Remote => 'success',
                        WorkType::Hybrid => 'warning',
                        WorkType::OnSite => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('contract_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? '-'),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('work_type')
                    ->options([
                        WorkType::Remote->value => WorkType::Remote->label(),
                        WorkType::OnSite->value => WorkType::OnSite->label(),
                        WorkType::Hybrid->value => WorkType::Hybrid->label(),
                    ]),
                Tables\Filters\SelectFilter::make('contract_type')
                    ->options([
                        ContractType::FullTime->value => ContractType::FullTime->label(),
                        ContractType::PartTime->value => ContractType::PartTime->label(),
                        ContractType::Contract->value => ContractType::Contract->label(),
                        ContractType::Internship->value => ContractType::Internship->label(),
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
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
            'index' => Pages\ListCareers::route('/'),
            'create' => Pages\CreateCareer::route('/create'),
            'edit' => Pages\EditCareer::route('/{record}/edit'),
        ];
    }
}