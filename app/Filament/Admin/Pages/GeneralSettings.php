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
            
            // Convert path strings to media IDs for CuratorPicker
            $logoMediaId = null;
            $faviconMediaId = null;
            
            if ($settings->logo_path) {
                $logoMedia = \Awcodes\Curator\Models\Media::where('path', $settings->logo_path)->first();
                $logoMediaId = $logoMedia?->id;
            }
            if ($settings->favicon_path) {
                $faviconMedia = \Awcodes\Curator\Models\Media::where('path', $settings->favicon_path)->first();
                $faviconMediaId = $faviconMedia?->id;
            }
            
            $this->form->fill([
                'site_name' => $settings->site_name ?? '',
                'tagline' => $settings->tagline ?? '',
                'logo_path' => $logoMediaId,
                'favicon_path' => $faviconMediaId,
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
                'logo_path' => null,
                'favicon_path' => null,
                'phone' => '',
                'email' => '',
                'address' => '',
                'header_scripts' => '',
                'footer_scripts' => '',
                'social_profiles' => [],
            ]);
        } catch (\Exception $e) {
            // Catch any other exceptions (database errors, etc.) and use defaults
            \Log::warning('Failed to load GeneralSettings in mount(): ' . $e->getMessage());
            $this->form->fill([
                'site_name' => config('app.name', 'هوشکس'),
                'tagline' => '',
                'logo_path' => null,
                'favicon_path' => null,
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
                    ->icon('heroicon-o-share')
                    ->schema([
                        Forms\Components\Repeater::make('social_profiles')
                            ->label('پروفایل‌های اجتماعی')
                            ->schema([
                                Forms\Components\Select::make('platform')
                                    ->label('پلتفرم')
                                    ->required()
                                    ->options(self::getSocialPlatformOptions())
                                    ->native(false)
                                    ->searchable()
                                    ->allowHtml()
                                    ->live(),
                                Forms\Components\TextInput::make('url')
                                    ->label('لینک')
                                    ->url()
                                    ->required()
                                    ->maxLength(500)
                                    ->placeholder('https://...')
                                    ->prefixIcon('heroicon-o-link'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('افزودن شبکه اجتماعی')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => match ($state['platform'] ?? null) {
                                'telegram' => '📨 تلگرام',
                                'instagram' => '📷 اینستاگرام',
                                'aparat' => '🎬 آپارات',
                                'youtube' => '▶️ یوتیوب',
                                'linkedin' => '💼 لینکدین',
                                default => null,
                            }),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = app(GeneralSettingsConfig::class);
        
        foreach ($data as $key => $value) {
            // CuratorPicker returns media IDs, convert to path strings for logo/favicon
            if (in_array($key, ['logo_path', 'favicon_path']) && is_int($value)) {
                $media = \Awcodes\Curator\Models\Media::find($value);
                $value = $media ? $media->path : null;
            }
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

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    protected static function getSocialPlatformOptions(): array
    {
        $icons = [
            'telegram' => '<svg class="w-5 h-5 inline-block me-2" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.287 5.906c-.778.324-2.334.994-4.666 2.01-.378.15-.577.298-.595.442-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294.26.006.549-.1.868-.32 2.179-1.471 3.304-2.214 3.374-2.23.05-.012.12-.026.166.016.047.041.042.12.037.141-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8.154 8.154 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629.093.06.183.125.27.187.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.426 1.426 0 0 0-.013-.315.337.337 0 0 0-.114-.217.526.526 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09z"/></svg>',
            'instagram' => '<svg class="w-5 h-5 inline-block me-2" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.705.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.29-.044z"/><path d="M8 4.838a3.162 3.162 0 1 0 0 6.324 3.162 3.162 0 0 0 0-6.324zM8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm4.967-6.714a.739.739 0 1 1-1.478 0 .739.739 0 0 1 1.478 0z"/></svg>',
            'aparat' => '<svg class="w-5 h-5 inline-block me-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 14.596a1.573 1.573 0 0 1-1.573 1.573h-.001a1.573 1.573 0 1 1 0-3.147h.001a1.573 1.573 0 0 1 1.573 1.574zm-2.541-3.085a1.572 1.572 0 1 1-3.143 0 1.572 1.572 0 0 1 3.143 0zm-3.265 5.628a1.572 1.572 0 1 1 0-3.143 1.572 1.572 0 0 1 0 3.143zm0-4.139a1.573 1.573 0 1 1 0-3.146 1.573 1.573 0 0 1 0 3.146zm0-4.042a1.573 1.573 0 1 1 0-3.145 1.573 1.573 0 0 1 0 3.145zm-2.542 5.628a1.573 1.573 0 1 1 0-3.147 1.573 1.573 0 0 1 0 3.147zm-3.44 0a1.573 1.573 0 1 1 0-3.147 1.573 1.573 0 0 1 0 3.147z"/></svg>',
            'youtube' => '<svg class="w-5 h-5 inline-block me-2" fill="currentColor" viewBox="0 0 16 16"><path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.007 2.007 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.007 2.007 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31.4 31.4 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.007 2.007 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A99.788 99.788 0 0 1 7.858 2h.193zM6.4 5.209v4.818l4.157-2.408L6.4 5.209z"/></svg>',
            'linkedin' => '<svg class="w-5 h-5 inline-block me-2" fill="currentColor" viewBox="0 0 16 16"><path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/></svg>',
        ];

        return [
            'telegram' => $icons['telegram'] . '<span>تلگرام</span>',
            'instagram' => $icons['instagram'] . '<span>اینستاگرام</span>',
            'aparat' => $icons['aparat'] . '<span>آپارات</span>',
            'youtube' => $icons['youtube'] . '<span>یوتیوب</span>',
            'linkedin' => $icons['linkedin'] . '<span>لینکدین</span>',
        ];
    }
}