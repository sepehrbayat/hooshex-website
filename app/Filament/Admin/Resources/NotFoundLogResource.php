<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Core\Models\NotFoundLog;
use App\Filament\Admin\Resources\NotFoundLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotFoundLogResource extends Resource
{
    protected static ?string $model = NotFoundLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationLabel = 'لاگ 404';

    protected static ?string $modelLabel = '404';

    protected static ?string $pluralModelLabel = '404 ها';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('hit_count')
                    ->label('تعداد')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('referer')
                    ->label('ارجاع‌دهنده')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('first_seen_at')
                    ->label('اولین مشاهده')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_seen_at')
                    ->label('آخرین مشاهده')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('create_redirect')
                    ->label('ایجاد تغییر مسیر')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('target_url')
                            ->label('URL مقصد')
                            ->required()
                            ->url(),
                        \Filament\Forms\Components\Select::make('status_code')
                            ->label('کد وضعیت')
                            ->options([
                                301 => '301 - Permanent',
                                302 => '302 - Temporary',
                            ])
                            ->default(301),
                    ])
                    ->action(function (NotFoundLog $record, array $data) {
                        \App\Domains\Core\Models\Redirect::create([
                            'source_url' => parse_url($record->url, PHP_URL_PATH),
                            'target_url' => $data['target_url'],
                            'status_code' => $data['status_code'],
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تغییر مسیر ایجاد شد')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('hit_count', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNotFoundLogs::route('/'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'SEO';
    }
}