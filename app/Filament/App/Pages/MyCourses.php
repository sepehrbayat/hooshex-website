<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Domains\Courses\Models\Enrollment;
use App\Filament\App\Pages\MyLicenses;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class MyCourses extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static string $view = 'filament.app.pages.my-courses';

    protected static ?string $navigationLabel = 'دوره‌های من';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Enrollment::query()
                    ->where('user_id', auth()->id())
                    ->with(['course.thumbnail', 'course.licenses' => function ($query) {
                        $query->where('user_id', auth()->id())->where('is_active', true);
                    }])
            )
            ->columns([
                Tables\Columns\ImageColumn::make('course.thumbnail')
                    ->label('تصویر')
                    ->getStateUsing(function ($record) {
                        $course = $record->course;
                        if ($course->thumbnail) {
                            return $course->thumbnail->url;
                        }
                        return $course->thumbnail_path;
                    })
                    ->defaultImageUrl(url('/images/placeholder-course.png')),
                Tables\Columns\TextColumn::make('course.title')
                    ->label('عنوان دوره')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrolled_at')
                    ->label('تاریخ ثبت‌نام')
                    ->dateTime('Y/m/d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('انقضا')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->placeholder('نامحدود'),
                Tables\Columns\TextColumn::make('progress')
                    ->label('پیشرفت')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_license')
                    ->label('لایسنس')
                    ->boolean()
                    ->getStateUsing(function (Enrollment $record) {
                        return $record->course->licenses()
                            ->where('user_id', auth()->id())
                            ->where('is_active', true)
                            ->exists();
                    })
                    ->trueIcon('heroicon-o-key')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('مشاهده')
                    ->url(fn (Enrollment $record) => route('courses.show', $record->course->slug))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('view_licenses')
                    ->label('لایسنس‌ها')
                    ->icon('heroicon-o-key')
                    ->url(fn (Enrollment $record) => MyLicenses::getUrl() . '?tableFilters[course_id][value]=' . $record->course_id)
                    ->visible(fn (Enrollment $record) => $record->course->licenses()->where('user_id', auth()->id())->exists()),
            ])
            ->defaultSort('enrolled_at', 'desc');
    }
}
