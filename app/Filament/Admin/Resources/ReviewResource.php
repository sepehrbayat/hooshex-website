<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Interactions\Review;
use App\Filament\Admin\Resources\ReviewResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'نقد و بررسی';

    protected static ?string $modelLabel = 'نقد';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'در انتظار',
                        'approved' => 'تایید شده',
                        'spam' => 'اسپم',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Select::make('rating')
                    ->label('امتیاز')
                    ->options([
                        1 => '1 ستاره',
                        2 => '2 ستاره',
                        3 => '3 ستاره',
                        4 => '4 ستاره',
                        5 => '5 ستاره',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('عنوان')
                    ->maxLength(255),
                Forms\Components\Textarea::make('body')
                    ->label('متن نقد')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('کاربر')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reviewable_type')
                    ->label('نوع')
                    ->badge(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('امتیاز')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        5, 4 => 'success',
                        3 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('وضعیت')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'spam',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        1 => '1 ستاره',
                        2 => '2 ستاره',
                        3 => '3 ستاره',
                        4 => '4 ستاره',
                        5 => '5 ستاره',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('تایید')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (Review $record) => $record->update(['status' => 'approved']))
                    ->visible(fn (Review $record) => $record->status !== 'approved'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('تایید انتخاب شده‌ها')
                        ->action(fn ($records) => $records->each->update(['status' => 'approved'])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'تعاملات';
    }
}