<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Courses\Models\Course;
use App\Domains\Auth\Models\User;
use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use RalphJSmit\Filament\SEO\SEO;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Illuminate\Support\Str;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'دوره‌ها';

    protected static ?string $modelLabel = 'دوره';

    protected static ?string $pluralModelLabel = 'دوره‌ها';

    protected static ?string $navigationGroup = 'آموزش';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('CourseForm')
                ->tabs([
                    // Tab 1: Basic Info (keep for title, slug, teacher, short_description, language)
                    Forms\Components\Tabs\Tab::make('Basic Info')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\Select::make('teacher_id')
                                ->relationship('teacher', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Forms\Components\Textarea::make('short_description')
                                ->rows(3)
                                ->maxLength(500)
                                ->columnSpanFull(),
                            Forms\Components\Select::make('language')
                                ->options([
                                    'fa' => 'Persian (فارسی)',
                                    'en' => 'English',
                                ])
                                ->default('fa')
                                ->required(),
                        ])->columns(2),

                    // Tab 2: Media
                    Forms\Components\Tabs\Tab::make('Media')
                        ->schema([
                            CuratorPicker::make('thumbnail_id')
                                ->label('Thumbnail Image')
                                ->directory('course-thumbnails')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->buttonLabel('Select or Upload Image')
                                ->constrained(true)
                                ->size('lg')
                                ->lazyLoad()
                                ->live() // Force update after selection
                                ->afterStateUpdated(fn ($livewire) => $livewire->dispatch('refresh-form'))
                                ->columnSpanFull(),
                            Forms\Components\Select::make('intro_video_provider')
                                ->options([
                                    'aparat' => 'Aparat',
                                    'youtube' => 'YouTube',
                                    'host' => 'Self Hosted',
                                ])
                                ->nullable()
                                ->live()
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('intro_video_id')
                                ->label('Video Link or ID')
                                ->helperText('Paste the full Aparat URL (e.g., https://www.aparat.com/v/kfrhl85), we will extract the ID')
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Forms\Set $set, $state, $get) {
                                    // Extract video ID from URL if it's a full URL
                                    if (empty($state)) {
                                        return;
                                    }

                                    // Check if it's an Aparat URL
                                    if (preg_match('/(?:aparat\.com\/v\/|embed\/)([\w-]+)/', $state, $matches)) {
                                        $set('intro_video_id', $matches[1]);
                                        // Also set provider if not already set
                                        if (empty($get('intro_video_provider'))) {
                                            $set('intro_video_provider', 'aparat');
                                        }
                                    }
                                    // Check if it's a YouTube URL
                                    elseif (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $state, $matches)) {
                                        $set('intro_video_id', $matches[1]);
                                        if (empty($get('intro_video_provider'))) {
                                            $set('intro_video_provider', 'youtube');
                                        }
                                    }
                                })
                                ->columnSpanFull(),
                        ]),

                    // Tab 3: Details
                    Forms\Components\Tabs\Tab::make('Details')
                        ->schema([
                            Forms\Components\Select::make('level')
                                ->options([
                                    CourseLevel::Beginner->value => 'مقدماتی',
                                    CourseLevel::Intermediate->value => 'متوسط',
                                    CourseLevel::Advanced->value => 'پیشرفته',
                                ])
                                ->nullable(),
                            Forms\Components\Select::make('course_type')
                                ->options([
                                    'recorded' => 'ضبط شده',
                                    'live' => 'زنده',
                                    'hybrid' => 'ترکیبی',
                                ])
                                ->nullable()
                                ->label('نوع دوره'),
                            Forms\Components\TextInput::make('duration')
                                ->maxLength(50)
                                ->helperText('e.g., "12h 30m"'),
                            Forms\Components\TextInput::make('total_hours')
                                ->numeric()
                                ->nullable()
                                ->label('مجموع ساعات')
                                ->helperText('مثلاً: 39'),
                            Forms\Components\TextInput::make('total_lessons')
                                ->numeric()
                                ->nullable()
                                ->label('تعداد جلسات')
                                ->helperText('تعداد کل جلسات دوره'),
                            Forms\Components\TextInput::make('students_count')
                                ->numeric()
                                ->nullable()
                                ->helperText('Manual override for student count'),
                            Forms\Components\TextInput::make('guarantee_text')
                                ->maxLength(255)
                                ->nullable()
                                ->helperText('مثلاً: ضمانت بازگشت 30 روزه')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('support_type')
                                ->maxLength(255)
                                ->nullable()
                                ->label('نوع پشتیبانی')
                                ->helperText('مثلاً: تلگرام، ایمیل، تیکتینگ')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->required()
                                ->default(0)
                                ->helperText('قیمت به تومان'),
                            Forms\Components\TextInput::make('sale_price')
                                ->numeric()
                                ->nullable()
                                ->helperText('قیمت تخفیف خورده'),
                            Forms\Components\Toggle::make('is_certificate_available')
                                ->default(true)
                                ->label('دریافت گواهینامه'),
                            Forms\Components\Toggle::make('has_lifetime_access')
                                ->default(true)
                                ->label('دسترسی مادام‌العمر'),
                            Forms\Components\Toggle::make('has_practice_files')
                                ->default(false)
                                ->label('فایل‌های تمرینی'),
                            Forms\Components\DateTimePicker::make('last_updated_at')
                                ->label('آخرین به‌روزرسانی')
                                ->nullable(),
                            Forms\Components\Select::make('status')
                                ->options([
                                    CourseStatus::Draft->value => 'پیش‌نویس',
                                    CourseStatus::Published->value => 'منتشر شده',
                                    CourseStatus::Archived->value => 'آرشیو شده',
                                ])
                                ->required()
                                ->default(CourseStatus::Draft->value),
                            Forms\Components\Toggle::make('is_featured')
                                ->label('دوره ویژه'),
                        ])->columns(2),

                    // Tab 4: Lists
                    Forms\Components\Tabs\Tab::make('Lists')
                        ->schema([
                            Forms\Components\RichEditor::make('description')
                                ->label('توضیحات دوره')
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('what_you_learn')
                                ->label('چیزهایی که یاد می‌گیرید')
                                ->simple(
                                    Forms\Components\TextInput::make('item')
                                        ->label('مورد')
                                        ->required()
                                )
                                ->defaultItems(0)
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('course_requirements')
                                ->label('الزامات دوره')
                                ->simple(
                                    Forms\Components\TextInput::make('item')
                                        ->label('الزام')
                                        ->required()
                                )
                                ->defaultItems(0)
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('course_includes')
                                ->label('این دوره شامل')
                                ->simple(
                                    Forms\Components\TextInput::make('item')
                                        ->label('مورد')
                                        ->required()
                                )
                                ->defaultItems(0)
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('prerequisites')
                                ->label('پیش‌نیازها')
                                ->simple(
                                    Forms\Components\TextInput::make('item')
                                        ->label('پیش‌نیاز')
                                        ->required()
                                )
                                ->defaultItems(0)
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('target_audience')
                                ->label('مخاطبان هدف')
                                ->simple(
                                    Forms\Components\TextInput::make('item')
                                        ->label('مخاطب')
                                        ->required()
                                )
                                ->defaultItems(0)
                                ->columnSpanFull(),
                        ]),

                    // Tab 5: Curriculum (managed via RelationManager - see tabs above)
                    Forms\Components\Tabs\Tab::make('Curriculum')
                        ->schema([
                            Forms\Components\Placeholder::make('curriculum_note')
                                ->label('')
                                ->content('Use the "Chapters" relation manager tab above to manage chapters and lessons.')
                                ->columnSpanFull(),
                        ])
                        ->visible(false), // Hide this tab since we use RelationManager

                    // Tab 6: SEO
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
                    ->sortable()
                    ->label('عنوان'),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->searchable()
                    ->sortable()
                    ->label('مدرس'),
                Tables\Columns\TextColumn::make('price')
                    ->money('IRR')
                    ->sortable()
                    ->label('قیمت'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('وضعیت'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('ویژه'),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable()
                    ->label('تاریخ انتشار'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        CourseStatus::Draft->value => 'Draft',
                        CourseStatus::Published->value => 'Published',
                        CourseStatus::Archived->value => 'Archived',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CourseResource\RelationManagers\ChaptersRelationManager::class,
            CourseResource\RelationManagers\LicensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => CourseResource\Pages\ListCourses::route('/'),
            'create' => CourseResource\Pages\CreateCourse::route('/create'),
            'edit' => CourseResource\Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}