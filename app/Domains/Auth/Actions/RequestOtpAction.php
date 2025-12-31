<?php

declare(strict_types=1);

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\Services\OtpService;

/**
 * Request OTP Action
 * Single-purpose action for requesting OTP
 */
class RequestOtpAction
{
    public function __construct(
        private readonly OtpService $otpService
    ) {}

    /**
     * Execute the action
     *
     * @param string $mobile The mobile number to send OTP to
     * @return array{ok: bool, expires_at: string}
     */
    public function execute(string $mobile): array
    {
        $result = $this->otpService->sendOtp($mobile);

        return [
            'ok' => true,
            'expires_at' => $result['expires_at']->toIso8601String(),
        ];
    }
}
