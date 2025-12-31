<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Domains\Auth\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithFormActions;

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

        // Update user data (email and mobile updates handled separately if changed)
        $updateData = [
            'name' => $data['name'],
            'bio' => $data['bio'] ?? null,
            'avatar_path' => $data['avatar_path'] ?? null,
        ];

        // Only update email if it has changed
        if ($data['email'] !== $user->email) {
            // Check if new email is already taken
            if (User::where('email', $data['email'])->where('id', '!=', $user->id)->exists()) {
                Notification::make()
                    ->title('این ایمیل قبلاً استفاده شده است')
                    ->danger()
                    ->send();
                return;
            }
            $updateData['email'] = $data['email'];
        }

        // Only update mobile if it has changed
        $newMobile = $data['mobile'] ?? null;
        if ($newMobile !== $user->mobile) {
            // Check if new mobile is already taken (if not empty)
            if (!empty($newMobile) && User::where('mobile', $newMobile)->where('id', '!=', $user->id)->exists()) {
                Notification::make()
                    ->title('این شماره موبایل قبلاً استفاده شده است')
                    ->danger()
                    ->send();
                return;
            }
            $updateData['mobile'] = $newMobile;
        }

        $user->update($updateData);

        Notification::make()
            ->title('پروفایل با موفقیت به‌روزرسانی شد')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('ذخیره')
                ->submit('save'),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
