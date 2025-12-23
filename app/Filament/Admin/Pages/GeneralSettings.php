<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Settings\GeneralSettings as GeneralSettingsConfig;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class GeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.admin.pages.general-settings';

    protected static ?string $navigationLabel = 'تنظیمات عمومی';

    protected static ?string $title = 'تنظیمات عمومی';

    public ?array $data = [];

    public function mount(): void
    {
        try {
            $settings = app(GeneralSettingsConfig::class);
            $this->form->fill([
                'site_name' => $settings->site_name ?? '',
                'tagline' => $settings->tagline ?? '',
                'logo_path' => $settings->logo_path ?? '',
                'favicon_path' => $settings->favicon_path ?? '',
                'phone' => $settings->phone ?? '',
                'email' => $settings->email ?? '',
                'address' => $settings->address ?? '',
                'header_scripts' => $settings->header_scripts ?? '',
                'footer_scripts' => $settings->footer_scripts ?? '',
                'social_profiles' => $settings->social_profiles ?? [],
            ]);
        } catch (\Spatie\LaravelSettings\Exceptions\MissingSettings $e) {
            // Settings not initialized yet, fill with empty values
            $this->form->fill([
                'site_name' => config('app.name', 'هوشکس'),
                'tagline' => '',
                'logo_path' => '',
                'favicon_path' => '',
                'phone' => '',
                'email' => '',
                'address' => '',
                'header_scripts' => '',
                'footer_scripts' => '',
                'social_profiles' => [],
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('هویت سایت')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('نام سایت')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tagline')
                            ->label('شعار سایت')
                            ->maxLength(255),
                        CuratorPicker::make('logo_path')
                            ->label('لوگو')
                            ->directory('logos'),
                        CuratorPicker::make('favicon_path')
                            ->label('فاوآیکن')
                            ->directory('favicons')
                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/svg+xml']),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('اطلاعات تماس')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('تلفن')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label('ایمیل')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('آدرس')
                            ->rows(3),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('اسکریپت‌ها')
                    ->schema([
                        Forms\Components\Textarea::make('header_scripts')
                            ->label('اسکریپت‌های هدر (Analytics, GTM, etc.)')
                            ->helperText('کدهای HTML/JavaScript که در بخش <head> قرار می‌گیرند')
                            ->rows(5)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('footer_scripts')
                            ->label('اسکریپت‌های فوتر (Chat widgets, etc.)')
                            ->helperText('کدهای HTML/JavaScript که قبل از </body> قرار می‌گیرند')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('شبکه‌های اجتماعی')
                    ->schema([
                        Forms\Components\Repeater::make('social_profiles')
                            ->label('پروفایل‌های اجتماعی')
                            ->schema([
                                Forms\Components\TextInput::make('platform')
                                    ->label('پلتفرم')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->required()
                                    ->maxLength(500),
                            ])
                            ->columns(2)
                            ->defaultItems(0),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = app(GeneralSettingsConfig::class);
        
        foreach ($data as $key => $value) {
            $settings->$key = $value;
        }
        
        $settings->save();

        Notification::make()
            ->title('تنظیمات با موفقیت ذخیره شد')
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
}