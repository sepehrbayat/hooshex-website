<?php

declare(strict_types=1);

namespace App\Domains\Auth\Services;

use App\Services\SmsIrClient;
use Illuminate\Support\Facades\Cache;

/**
 * OTP Service
 * Handles OTP generation, sending, and verification
 */
class OtpService
{
    private const OTP_TTL_MINUTES = 5;
    private const OTP_CACHE_PREFIX = 'otp:';

    public function __construct(
        private readonly SmsIrClient $smsClient
    ) {}

    /**
     * Generate and send OTP to the given mobile number
     *
     * @param string $mobile The mobile number to send OTP to
     * @return array{code: string, expires_at: \Carbon\Carbon}
     */
    public function sendOtp(string $mobile): array
    {
        $code = $this->generateCode();
        $expiresAt = now()->addMinutes(self::OTP_TTL_MINUTES);

        Cache::put($this->cacheKey($mobile), $code, $expiresAt);

        $templateId = config('services.sms_ir.otp_template_id');
        $this->smsClient->sendUltraFastOtp($mobile, (string) $templateId, ['code' => $code]);

        return [
            'code' => $code,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Verify the OTP code for the given mobile number
     *
     * @param string $mobile The mobile number
     * @param string $code The OTP code to verify
     * @return bool Whether the code is valid
     */
    public function verifyOtp(string $mobile, string $code): bool
    {
        $cachedCode = Cache::pull($this->cacheKey($mobile));

        return $cachedCode === $code;
    }

    /**
     * Check if an OTP exists for the given mobile number
     */
    public function hasActiveOtp(string $mobile): bool
    {
        return Cache::has($this->cacheKey($mobile));
    }

    /**
     * Generate a random OTP code
     */
    private function generateCode(): string
    {
        return (string) random_int(10000, 99999);
    }

    /**
     * Get the cache key for the given mobile number
     */
    private function cacheKey(string $mobile): string
    {
        return self::OTP_CACHE_PREFIX . $mobile;
    }
}
