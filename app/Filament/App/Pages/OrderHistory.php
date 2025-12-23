<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Domains\Commerce\Models\Order;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class OrderHistory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string $view = 'filament.app.pages.order-history';

    protected static ?string $navigationLabel = 'سفارش‌های من';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->where('user_id', auth()->id())
                    ->with(['items.orderable', 'user'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('شماره سفارش')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('مبلغ')
                    ->money('IRR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('دانلود فاکتور')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Order $record) => route('app.invoice.download', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
