<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Courses\Models\Teacher;
use App\Filament\Admin\Resources\TeacherResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use RalphJSmit\Filament\SEO\SEO;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('TeacherForm')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')
                        ->schema([
                            Forms\Components\Select::make('user_id')
                                ->relationship('user', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('specialty')
                                ->maxLength(255),
                            CuratorPicker::make('avatar_id')
                                ->label('آواتار')
                                ->directory('teachers')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                            Forms\Components\Toggle::make('is_featured'),
                            Forms\Components\DateTimePicker::make('published_at'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Bio')
                        ->schema([
                            Forms\Components\RichEditor::make('bio')
                                ->columnSpanFull(),
                            Forms\Components\KeyValue::make('social_links')
                                ->keyLabel('Platform')
                                ->valueLabel('URL')
                                ->helperText('Social media links (e.g., twitter, linkedin, website)')
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
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialty')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}