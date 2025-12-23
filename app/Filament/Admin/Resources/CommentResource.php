<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Interactions\Comment;
use App\Filament\Admin\Resources\CommentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'نظرات';

    protected static ?string $modelLabel = 'نظر';

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
                        'trash' => 'حذف شده',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('body')
                    ->label('متن نظر')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'body')
                    ->label('پاسخ به')
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('کاربر')
                    ->searchable(),
                Tables\Columns\TextColumn::make('body')
                    ->label('متن')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('commentable_type')
                    ->label('نوع')
                    ->badge(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'در انتظار',
                        'approved' => 'تایید شده',
                        'spam' => 'اسپم',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('تایید')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (Comment $record) => $record->update(['status' => 'approved']))
                    ->visible(fn (Comment $record) => $record->status !== 'approved'),
                Tables\Actions\Action::make('spam')
                    ->label('اسپم')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->action(fn (Comment $record) => $record->update(['status' => 'spam'])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('تایید انتخاب شده‌ها')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['status' => 'approved'])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'تعاملات';
    }
}