<?php

declare(strict_types=1);

namespace App\Domains\Auth\Data;

use Carbon\Carbon;

/**
 * OTP Data Transfer Object
 */
readonly class OtpData
{
    public function __construct(
        public string $mobile,
        public string $code,
        public Carbon $expiresAt,
    ) {}

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            mobile: $data['mobile'],
            code: $data['code'],
            expiresAt: $data['expires_at'] instanceof Carbon 
                ? $data['expires_at'] 
                : Carbon::parse($data['expires_at']),
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'mobile' => $this->mobile,
            'code' => $this->code,
            'expires_at' => $this->expiresAt->toIso8601String(),
        ];
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expiresAt->isPast();
    }
}
