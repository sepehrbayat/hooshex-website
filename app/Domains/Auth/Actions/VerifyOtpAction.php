<?php

declare(strict_types=1);

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\Events\UserRegistered;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Verify OTP Action
 * Single-purpose action for verifying OTP and logging in/registering user
 */
class VerifyOtpAction
{
    public function __construct(
        private readonly OtpService $otpService
    ) {}

    /**
     * Execute the action
     *
     * @param string $mobile The mobile number
     * @param string $code The OTP code
     * @param string|null $name Optional user name
     * @return array{ok: bool, user?: array, message?: string}
     */
    public function execute(string $mobile, string $code, ?string $name = null): array
    {
        if (!$this->otpService->verifyOtp($mobile, $code)) {
            return [
                'ok' => false,
                'message' => 'Invalid code',
            ];
        }

        $user = $this->findOrCreateUser($mobile, $name);

        Auth::login($user);

        return [
            'ok' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'mobile' => $user->mobile,
            ],
        ];
    }

    /**
     * Find existing user or create new one
     */
    private function findOrCreateUser(string $mobile, ?string $name): User
    {
        $user = User::firstOrNew(['mobile' => $mobile]);
        $isNewUser = !$user->exists;

        if ($isNewUser) {
            $user->username = $this->usernameFromMobile($mobile);
            $user->email = $user->username . '@example.local';
        }

        $user->name = $name ?? $user->name;
        $user->password = Str::password();
        $user->legacy_password = null;
        $user->save();

        if ($isNewUser) {
            event(new UserRegistered($user));
        }

        return $user;
    }

    /**
     * Generate username from mobile number
     */
    private function usernameFromMobile(string $mobile): string
    {
        return 'user_' . preg_replace('/\\D/', '', $mobile);
    }
}
