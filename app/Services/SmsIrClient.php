<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsIrClient
{
    public function sendUltraFastOtp(string $mobile, string $templateId, array $parameters): bool
    {
        $apiKey = config('services.sms_ir.api_key');
        $lineNumber = config('services.sms_ir.line_number');
        $url = rtrim(config('services.sms_ir.base_url', 'https://api.sms.ir/v1'), '/').'/send/verify';

        if (empty($apiKey) || empty($lineNumber)) {
            Log::warning('SMS.ir credentials missing');
            return false;
        }

        $response = Http::withHeaders([
            'X-API-KEY' => $apiKey,
        ])->post($url, [
            'mobile' => $mobile,
            'templateId' => $templateId,
            'lineNumber' => $lineNumber,
            'parameters' => collect($parameters)
                ->map(fn ($value, $name) => ['name' => $name, 'value' => $value])
                ->values()
                ->all(),
        ]);

        if ($response->failed()) {
            Log::error('SMS.ir OTP send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return (bool) ($response->json('status') ?? true);
    }
}

