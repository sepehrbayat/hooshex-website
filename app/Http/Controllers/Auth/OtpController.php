<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RequestOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Domains\Auth\Models\User;
use App\Services\SmsIrClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    public function __construct(private readonly SmsIrClient $sms)
    {
    }

    public function requestOtp(RequestOtpRequest $request)
    {
        $data = $request->validated();

        $code = (string) random_int(10000, 99999);
        $ttl = now()->addMinutes(5);

        Cache::put($this->cacheKey($data['mobile']), $code, $ttl);

        $templateId = config('services.sms_ir.otp_template_id');
        $this->sms->sendUltraFastOtp($data['mobile'], (string) $templateId, ['code' => $code]);

        return response()->json([
            'ok' => true,
            'expires_at' => $ttl->toIso8601String(),
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $data = $request->validated();

        $cached = Cache::pull($this->cacheKey($data['mobile']));
        if ($cached !== $data['code']) {
            return response()->json(['ok' => false, 'message' => 'Invalid code'], 422);
        }

        $user = User::firstOrNew(['mobile' => $data['mobile']]);
        if (! $user->exists) {
            $user->username = $this->usernameFromMobile($data['mobile']);
            $user->email = $user->username.'@example.local';
        }

        $user->name = $data['name'] ?? $user->name;
        $user->password = Str::password();
        $user->legacy_password = null;
        $user->save();

        Auth::login($user);

        return response()->json([
            'ok' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'mobile' => $user->mobile,
            ],
        ]);
    }

    private function cacheKey(string $mobile): string
    {
        return 'otp:'.$mobile;
    }

    private function usernameFromMobile(string $mobile): string
    {
        return 'user_'.preg_replace('/\\D/', '', $mobile);
    }
}

