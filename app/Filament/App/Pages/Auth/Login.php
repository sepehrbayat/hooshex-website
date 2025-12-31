<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected static string $view = 'filament.app.pages.auth.login';

    public function getHeading(): string | Htmlable
    {
        return 'ورود به پنل کاربری';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'هوشکس - مرجع هوش مصنوعی فارسی';
    }

    public function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('ایمیل')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1, 'dir' => 'ltr', 'class' => 'text-left']);
    }

    public function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('رمز عبور')
            ->password()
            ->required()
            ->extraInputAttributes(['tabindex' => 2, 'dir' => 'ltr', 'class' => 'text-left']);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}

