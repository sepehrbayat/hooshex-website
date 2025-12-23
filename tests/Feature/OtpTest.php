<?php

declare(strict_types=1);

use App\Services\SmsIrClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('requests otp successfully', function () {
    $this->app->instance(SmsIrClient::class, new class extends SmsIrClient {
        public function sendUltraFastOtp(string $mobile, string $templateId, array $parameters): bool
        {
            return true;
        }
    });

    $response = $this->postJson('/auth/otp/request', [
        'mobile' => '+989111111111',
    ]);

    $response->assertStatus(200);
});

it('verifies otp from cache', function () {
    Cache::put('otp:+989111111111', '12345', now()->addMinutes(5));

    $response = $this->postJson('/auth/otp/verify', [
        'mobile' => '+989111111111',
        'code' => '12345',
        'name' => 'Tester',
    ]);

    $response->assertStatus(200)->assertJsonPath('ok', true);
});

