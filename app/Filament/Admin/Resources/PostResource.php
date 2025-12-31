<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Domains\Blog\Models\Post;
use App\Domains\Core\Models\Category;
use App\Enums\PostStatus;
use App\Enums\PostType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use RalphJSmit\Filament\SEO\SEO;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'مقالات';

    protected static ?string $modelLabel = 'مقاله';

    protected static ?string $pluralModelLabel = 'مقالات';

    protected static ?string $navigationGroup = 'محتوا';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        // Main Content Area (Right - 8 columns)
                        Forms\Components\Group::make([
                            Forms\Components\Section::make()
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('عنوان')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                            if (empty($get('slug'))) {
                                                $set('slug', \Illuminate\Support\Str::slug($state));
                                            }
                                        })
                                        ->extraInputAttributes(['class' => 'text-lg font-semibold text-[#22165E]']),
                                    
                                    Forms\Components\TextInput::make('slug')
                                        ->label('نامک یکتا (Slug)')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->alphaDash()
                                        ->prefix('/')
                                        ->helperText('آدرس URL مقاله')
                                        ->extraInputAttributes(['class' => 'text-[#2D2D2D]']),
                                    
                                    Forms\Components\RichEditor::make('content')
                                        ->label('متن مقاله')
                                        ->toolbarButtons([
                                            'bold',
                                            'italic',
                                            'underline',
                                            'strike',
                                            'link',
                                            'h2',
                                            'h3',
                                            'bulletList',
                                            'orderedList',
                                            'blockquote',
                                            'codeBlock',
                                        ])
                                        ->required()
                                        ->extraAttributes(['style' => 'min-height: 400px;'])
                                        ->columnSpanFull(),
                                    
                                    Forms\Components\Textarea::make('excerpt')
                                        ->label('خلاصه مقاله')
                                        ->rows(4)
                                        ->helperText('اگر خالی بماند، خودکار از متن اصلی استخراج می‌شود')
                                        ->extraInputAttributes(['class' => 'text-[#2D2D2D]'])
                                        ->columnSpanFull(),
                                ])
                                ->extraAttributes([
                                    'class' => 'rounded-2xl shadow-sm bg-white p-8 border border-[#FCF1FB]',
                                ]),
                        ])
                        ->columnSpan(['lg' => 8]),

                        // Sidebar Area (Left - 4 columns)
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('تنظیمات انتشار')
                                ->schema([
                                    Forms\Components\Select::make('status')
                                        ->label('وضعیت')
                                        ->options([
                                            PostStatus::Draft->value => '📝 پیش‌نویس',
                                            PostStatus::Published->value => '✅ منتشر شده',
                                            PostStatus::Scheduled->value => '⏰ زمان‌بندی شده',
                                        ])
                                        ->required()
                                        ->default(PostStatus::Draft->value)
                                        ->native(false)
                                        ->extraAttributes(['class' => 'font-semibold text-[#22165E]']),
                                    
                                    Forms\Components\DateTimePicker::make('published_at')
                                        ->label('تاریخ انتشار')
                                        ->helperText('برای زمان‌بندی، تاریخ آینده انتخاب کنید')
                                        ->seconds(false)
                                        ->native(false)
                                        ->extraInputAttributes(['class' => 'text-[#2D2D2D]']),
                                    
                                    Forms\Components\Select::make('author_id')
                                        ->label('نویسنده')
                                        ->relationship('author', 'name')
                                        ->required()
                                        ->default(fn () => auth()->id())
                                        ->searchable()
                                        ->preload()
                                        ->native(false),
                                    
                                    Forms\Components\Placeholder::make('reading_time')
                                        ->label('⏱ زمان مطالعه')
                                        ->content(fn ($record) => $record?->reading_time 
                                            ? $record->reading_time . ' دقیقه' 
                                            : 'محاسبه خودکار پس از ذخیره')
                                        ->extraAttributes(['class' => 'text-sm text-[#AAAAAA]']),
                                ])
                                ->extraAttributes([
                                    'class' => 'rounded-2xl shadow-sm bg-white p-6 mb-6 border border-[#FCF1FB]',
                                ]),
                            
                            Forms\Components\Section::make('تصویر شاخص')
                                ->schema([
                                    CuratorPicker::make('thumbnail_id')
                                        ->label('انتخاب تصویر')
                                        ->directory('thumbnails')
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->buttonLabel('انتخاب تصویر')
                                        ->afterStateUpdated(function (\Awcodes\Curator\Components\Forms\CuratorPicker $component, mixed $state, \Livewire\Component $livewire): void {
                                            if ($livewire instanceof \Filament\Resources\Pages\EditRecord) {
                                                $livewire->saveFormComponentOnly($component);
                                            }
                                        })
                                        ->columnSpanFull(),
                                ])
                                ->extraAttributes([
                                    'class' => 'rounded-2xl shadow-sm bg-white p-6 mb-6 border border-[#FCF1FB]',
                                ]),
                            
                            Forms\Components\Section::make('دسته‌بندی')
                                ->schema([
                                    Forms\Components\Select::make('primary_category_id')
                                        ->label('دسته اصلی')
                                        ->relationship('primaryCategory', 'name', fn ($query) => $query->where('type', 'post'))
                                        ->searchable()
                                        ->preload()
                                        ->native(false)
                                        ->helperText('دسته‌بندی اصلی برای URL')
                                        ->extraAttributes(['class' => 'text-[#2D2D2D]']),
                                    
                                    Forms\Components\Select::make('categories')
                                        ->label('دسته‌بندی‌های فرعی')
                                        ->multiple()
                                        ->relationship('categories', 'name', fn ($query) => $query->where('type', 'post'))
                                        ->preload()
                                        ->searchable()
                                        ->native(false),
                                    
                                    Forms\Components\Select::make('type')
                                        ->label('نوع مقاله')
                                        ->options([
                                            PostType::Article->value => '📄 مقاله',
                                            PostType::News->value => '📰 خبر',
                                        ])
                                        ->required()
                                        ->default(PostType::Article->value)
                                        ->native(false),
                                ])
                                ->extraAttributes([
                                    'class' => 'rounded-2xl shadow-sm bg-white p-6 border border-[#FCF1FB]',
                                ]),
                        ])
                        ->columnSpan(['lg' => 4]),
                    ]),

                // SEO Section (Full Width)
                Forms\Components\Section::make('بهینه‌سازی موتورهای جستجو (SEO)')
                    ->schema([
                        Forms\Components\TagsInput::make('focus_keywords')
                            ->label('کلمات کلیدی اصلی (Focus Keywords)')
                            ->placeholder('کلمه کلیدی اول = هدف، بقیه = کمکی')
                            ->helperText('کلمه اول: هدف اصلی | کلمات بعدی: کلیدهای کمکی و مرتبط')
                            ->separator(',')
                            ->splitKeys(['Tab', ','])
                            ->columnSpanFull(),
                        SEO::make(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->extraAttributes([
                        'class' => 'rounded-2xl shadow-sm bg-white border border-[#FCF1FB] mt-8',
                    ]),

                // SEO Analyzer Widget (Full Width)
                Forms\Components\Section::make('تحلیل SEO و خوانایی')
                    ->description('تحلیل جامع محتوا برای بهینه‌سازی موتورهای جستجو')
                    ->schema([
                        Forms\Components\Placeholder::make('seo_analyzer')
                            ->content(fn () => view('filament.widgets.seo-analyzer'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->extraAttributes([
                        'class' => 'rounded-2xl shadow-sm bg-white border border-[#FCF1FB] mt-4',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->value ?? 'N/A'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->value ?? 'N/A'),
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        PostType::Article->value => 'Article',
                        PostType::News->value => 'News',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        PostStatus::Draft->value => 'Draft',
                        PostStatus::Published->value => 'Published',
                        PostStatus::Scheduled->value => 'Scheduled',
                    ]),
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
            'index' => PostResource\Pages\ListPosts::route('/'),
            'create' => PostResource\Pages\CreatePost::route('/create'),
            'edit' => PostResource\Pages\EditPost::route('/{record}/edit'),
        ];
    }
}