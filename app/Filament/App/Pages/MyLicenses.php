<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Domains\Courses\Models\CourseLicense;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class MyLicenses extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static string $view = 'filament.app.pages.my-licenses';

    protected static ?string $navigationLabel = 'لایسنس‌های من';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CourseLicense::query()
                    ->where('user_id', auth()->id())
                    ->with(['course.thumbnail', 'order'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('course.title')
                    ->label('دوره')
                    ->searchable()
                    ->sortable()
                    ->description(fn (CourseLicense $record) => $record->course->short_description),
                Tables\Columns\TextColumn::make('license_key')
                    ->label('کلید لایسنس')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('تاریخ انقضا')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->placeholder('مادام‌العمر')
                    ->color(fn (CourseLicense $record) => $record->isExpired() ? 'danger' : null),
                Tables\Columns\TextColumn::make('order.id')
                    ->label('سفارش')
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : '-')
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
                    ->query(fn ($query) => $query->where('expires_at', '<', now())->where('is_active', true)),
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('دوره')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_course')
                    ->label('مشاهده دوره')
                    ->icon('heroicon-o-eye')
                    ->url(fn (CourseLicense $record) => route('courses.show', $record->course->slug))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('هیچ لایسنسی یافت نشد')
            ->emptyStateDescription('شما هنوز هیچ لایسنسی برای دوره‌ها ندارید.');
    }
}

