<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Commerce\Models\Order;
use App\Enums\OrderStatus;
use App\Filament\Admin\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Filament\Admin\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Admin\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Admin\Resources\OrderResource\Pages\ViewOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    protected static ?string $navigationLabel = 'سفارش‌ها';

    protected static ?string $modelLabel = 'سفارش';

    protected static ?string $pluralModelLabel = 'سفارش‌ها';

    protected static ?string $navigationGroup = 'تجارت';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $statusOptions = collect(OrderStatus::cases())
            ->mapWithKeys(fn (OrderStatus $case) => [$case->value => $case->label()])
            ->all();

        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات سفارش')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('کاربر')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('وضعیت')
                            ->options($statusOptions)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('total_amount')
                            ->label('مبلغ کل')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('gateway')
                            ->label('درگاه')
                            ->disabled(),

                        Forms\Components\TextInput::make('gateway_ref_id')
                            ->label('Ref ID درگاه')
                            ->disabled(),

                        Forms\Components\TextInput::make('transaction_id')
                            ->label('شناسه تراکنش')
                            ->disabled(),

                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP')
                            ->disabled(),

                        Forms\Components\KeyValue::make('billing_address')
                            ->label('آدرس صورتحساب')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user')->withCount(['items', 'licenses']))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('شماره')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.mobile')
                    ->label('موبایل')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        if ($state instanceof OrderStatus) {
                            return $state->label();
                        }

                        return (string) $state;
                    })
                    ->color(function ($state): string {
                        $value = $state instanceof OrderStatus ? $state->value : (string) $state;

                        return match ($value) {
                            OrderStatus::Pending->value => 'warning',
                            OrderStatus::Paid->value => 'success',
                            OrderStatus::Failed->value => 'danger',
                            OrderStatus::Refunded->value => 'gray',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('مبلغ')
                    ->money('IRR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('gateway')
                    ->label('درگاه')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('آیتم‌ها')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('licenses_count')
                    ->label('لایسنس‌ها')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        OrderStatus::Pending->value => OrderStatus::Pending->label(),
                        OrderStatus::Paid->value => OrderStatus::Paid->label(),
                        OrderStatus::Failed->value => OrderStatus::Failed->label(),
                        OrderStatus::Refunded->value => OrderStatus::Refunded->label(),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
