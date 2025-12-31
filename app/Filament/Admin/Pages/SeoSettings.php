<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Settings\SeoSettings as SeoSettingsConfig;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class SeoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string $view = 'filament.admin.pages.seo-settings';

    protected static ?string $navigationLabel = 'تنظیمات SEO';

    protected static ?string $title = 'تنظیمات SEO';

    public ?array $data = [];

    public function mount(): void
    {
        try {
            $settings = app(SeoSettingsConfig::class);
            $this->form->fill([
                'title_separator' => $settings->title_separator ?? ' | ',
                'title_suffix' => $settings->title_suffix ?? '',
                'noindex_tags' => $settings->noindex_tags ?? false,
                'noindex_categories' => $settings->noindex_categories ?? false,
                'noindex_search' => $settings->noindex_search ?? false,
                'default_schema_ai_tools' => $settings->default_schema_ai_tools ?? 'SoftwareApplication',
                'default_schema_posts' => $settings->default_schema_posts ?? 'Article',
                'default_schema_courses' => $settings->default_schema_courses ?? 'Course',
                'default_schema_products' => $settings->default_schema_products ?? 'Product',
                'include_ai_tools_in_sitemap' => $settings->include_ai_tools_in_sitemap ?? true,
                'include_posts_in_sitemap' => $settings->include_posts_in_sitemap ?? true,
                'include_news_in_sitemap' => $settings->include_news_in_sitemap ?? true,
                'include_courses_in_sitemap' => $settings->include_courses_in_sitemap ?? true,
                'include_products_in_sitemap' => $settings->include_products_in_sitemap ?? true,
                'include_teachers_in_sitemap' => $settings->include_teachers_in_sitemap ?? true,
                'include_pages_in_sitemap' => $settings->include_pages_in_sitemap ?? true,
                'include_careers_in_sitemap' => $settings->include_careers_in_sitemap ?? true,
            ]);
        } catch (\Spatie\LaravelSettings\Exceptions\MissingSettings $e) {
            // Settings not initialized yet, initialize them with default values
            $defaults = [
                'title_separator' => ' | ',
                'title_suffix' => '',
                'noindex_tags' => false,
                'noindex_categories' => false,
                'noindex_search' => false,
                'default_schema_ai_tools' => 'SoftwareApplication',
                'default_schema_posts' => 'Article',
                'default_schema_courses' => 'Course',
                'default_schema_products' => 'Product',
                'include_ai_tools_in_sitemap' => true,
                'include_posts_in_sitemap' => true,
                'include_news_in_sitemap' => true,
                'include_courses_in_sitemap' => true,
                'include_products_in_sitemap' => true,
                'include_teachers_in_sitemap' => true,
                'include_pages_in_sitemap' => true,
                'include_careers_in_sitemap' => true,
            ];
            
            // Initialize settings in database
            foreach ($defaults as $name => $value) {
                $payload = $value === null ? json_encode('') : json_encode($value);
                DB::table('settings')->updateOrInsert(
                    ['name' => $name, 'group' => 'seo'],
                    ['payload' => $payload, 'created_at' => now(), 'updated_at' => now()]
                );
            }
            
            // Now fill the form with default values
            $this->form->fill($defaults);
        } catch (\Exception $e) {
            // Catch any other exceptions (database errors, etc.) and use defaults
            \Log::warning('Failed to load SeoSettings in mount(): ' . $e->getMessage());
            $this->form->fill([
                'title_separator' => ' | ',
                'title_suffix' => '',
                'noindex_tags' => false,
                'noindex_categories' => false,
                'noindex_search' => false,
                'default_schema_ai_tools' => 'SoftwareApplication',
                'default_schema_posts' => 'Article',
                'default_schema_courses' => 'Course',
                'default_schema_products' => 'Product',
                'include_ai_tools_in_sitemap' => true,
                'include_posts_in_sitemap' => true,
                'include_news_in_sitemap' => true,
                'include_courses_in_sitemap' => true,
                'include_products_in_sitemap' => true,
                'include_teachers_in_sitemap' => true,
                'include_pages_in_sitemap' => true,
                'include_careers_in_sitemap' => true,
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('پیش‌فرض‌های عنوان')
                    ->schema([
                        Forms\Components\TextInput::make('title_separator')
                            ->label('جداکننده عنوان')
                            ->default(' | ')
                            ->required()
                            ->maxLength(10),
                        Forms\Components\TextInput::make('title_suffix')
                            ->label('پسوند عنوان')
                            ->helperText('مثلاً: - هوشکس')
                            ->maxLength(100),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('کنترل ایندکس‌گذاری')
                    ->schema([
                        Forms\Components\Toggle::make('noindex_tags')
                            ->label('noindex برای صفحات برچسب‌ها')
                            ->default(false),
                        Forms\Components\Toggle::make('noindex_categories')
                            ->label('noindex برای صفحات دسته‌بندی‌ها')
                            ->default(false),
                        Forms\Components\Toggle::make('noindex_search')
                            ->label('noindex برای صفحات جستجو')
                            ->default(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('پیش‌فرض Schema Type')
                    ->schema([
                        Forms\Components\Select::make('default_schema_ai_tools')
                            ->label('AiTools')
                            ->options([
                                'SoftwareApplication' => 'SoftwareApplication',
                                'Product' => 'Product',
                                'Thing' => 'Thing',
                            ])
                            ->default('SoftwareApplication'),
                        Forms\Components\Select::make('default_schema_posts')
                            ->label('Posts')
                            ->options([
                                'Article' => 'Article',
                                'BlogPosting' => 'BlogPosting',
                                'NewsArticle' => 'NewsArticle',
                            ])
                            ->default('Article'),
                        Forms\Components\Select::make('default_schema_courses')
                            ->label('Courses')
                            ->options([
                                'Course' => 'Course',
                                'EducationalOccupationalProgram' => 'EducationalOccupationalProgram',
                            ])
                            ->default('Course'),
                        Forms\Components\Select::make('default_schema_products')
                            ->label('Products')
                            ->options([
                                'Product' => 'Product',
                                'SoftwareApplication' => 'SoftwareApplication',
                            ])
                            ->default('Product'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('کنترل Sitemap')
                    ->schema([
                        Forms\Components\Toggle::make('include_ai_tools_in_sitemap')
                            ->label('شامل AiTools در sitemap')
                            ->default(true),
                        Forms\Components\Toggle::make('include_posts_in_sitemap')
                            ->label('شامل Posts در sitemap')
                            ->default(true),
                        Forms\Components\Toggle::make('include_news_in_sitemap')
                            ->label('شامل News در sitemap')
                            ->default(true),
                        Forms\Components\Toggle::make('include_courses_in_sitemap')
                            ->label('شامل Courses در sitemap')
                            ->default(true),
                        Forms\Components\Toggle::make('include_products_in_sitemap')
                            ->label('شامل Products در sitemap')
                            ->default(true),
                        Forms\Components\Toggle::make('include_teachers_in_sitemap')
                            ->label('شامل Teachers در sitemap')
                            ->default(true),
                        Forms\Components\Toggle::make('include_pages_in_sitemap')
                            ->label('شامل Pages در sitemap')
                            ->default(true),
                        Forms\Components\Toggle::make('include_careers_in_sitemap')
                            ->label('شامل Careers در sitemap')
                            ->default(true),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = app(SeoSettingsConfig::class);
        
        foreach ($data as $key => $value) {
            $settings->$key = $value;
        }
        
        $settings->save();

        Notification::make()
            ->title('تنظیمات SEO با موفقیت ذخیره شد')
            ->success()
            ->send();
    }

    protected function getCachedFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('ذخیره')
                ->submit('save'),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}