<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Domains\Auth\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static string $view = 'filament.app.pages.profile';

    protected static ?string $navigationLabel = 'پروفایل';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'bio' => $user->bio,
            'avatar_path' => $user->avatar_path,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات شخصی')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('نام')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('ایمیل')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('mobile')
                            ->label('موبایل')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Textarea::make('bio')
                            ->label('بیوگرافی')
                            ->rows(3),
                        Forms\Components\FileUpload::make('avatar_path')
                            ->label('آواتار')
                            ->image()
                            ->directory('avatars'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('تغییر رمز عبور')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('رمز فعلی')
                            ->password()
                            ->required(fn ($get) => !empty($get('new_password'))),
                        Forms\Components\TextInput::make('new_password')
                            ->label('رمز جدید')
                            ->password()
                            ->minLength(8),
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('تکرار رمز جدید')
                            ->password()
                            ->same('new_password'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        if (!empty($data['current_password'])) {
            if (!Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->title('رمز فعلی اشتباه است')
                    ->danger()
                    ->send();
                return;
            }

            $user->password = Hash::make($data['new_password']);
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'] ?? null,
            'bio' => $data['bio'] ?? null,
            'avatar_path' => $data['avatar_path'] ?? null,
        ]);

        Notification::make()
            ->title('پروفایل با موفقیت به‌روزرسانی شد')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('ذخیره')
                ->submit('save'),
        ];
    }
}
