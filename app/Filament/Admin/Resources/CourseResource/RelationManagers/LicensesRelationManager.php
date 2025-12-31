<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CourseResource\RelationManagers;

use App\Domains\Courses\Models\CourseLicense;
use App\Domains\Courses\Actions\AssignLicenseAction;
use App\Domains\Courses\Actions\GenerateLicenseKeyAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    protected static ?string $title = 'لایسنس‌ها';

    protected static ?string $label = 'لایسنس';

    protected static ?string $pluralLabel = 'لایسنس‌ها';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('کاربر')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('license_key')
                    ->label('کلید لایسنس')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true, table: CourseLicense::class, column: 'license_key')
                    ->default(fn () => app(GenerateLicenseKeyAction::class)->handle())
                    ->required(),
                Forms\Components\Select::make('order_id')
                    ->label('سفارش')
                    ->relationship('order', 'id', modifyQueryUsing: fn (Builder $query) => $query->where('status', 'paid'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('تاریخ انقضا')
                    ->nullable()
                    ->helperText('خالی بگذارید برای دسترسی مادام‌العمر'),
                Forms\Components\Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true)
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('یادداشت')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('license_key')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('license_key')
                    ->label('کلید لایسنس')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.id')
                    ->label('سفارش')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : '-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('تاریخ انقضا')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->placeholder('مادام‌العمر'),
                Tables\Columns\TextColumn::make('assignedBy.name')
                    ->label('اختصاص داده شده توسط')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('فعال'),
                Tables\Filters\Filter::make('expired')
                    ->label('منقضی شده')
                    ->query(fn (Builder $query) => $query->where('expires_at', '<', now())->where('is_active', true)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('افزودن لایسنس')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['course_id'] = $this->getOwnerRecord()->id;
                        $data['assigned_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
