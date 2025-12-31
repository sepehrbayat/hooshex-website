<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RequestOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Domains\Auth\Actions\RequestOtpAction;
use App\Domains\Auth\Actions\VerifyOtpAction;
use Illuminate\Http\JsonResponse;

/**
 * OTP Controller
 * Thin controller that delegates to domain actions
 */
class OtpController extends Controller
{
    public function __construct(
        private readonly RequestOtpAction $requestOtpAction,
        private readonly VerifyOtpAction $verifyOtpAction
    ) {}

    /**
     * Request OTP for the given mobile number
     */
    public function requestOtp(RequestOtpRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->requestOtpAction->execute($data['mobile']);

        return response()->json($result);
    }

    /**
     * Verify OTP and login/register user
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->verifyOtpAction->execute(
            mobile: $data['mobile'],
            code: $data['code'],
            name: $data['name'] ?? null
        );

        if (!$result['ok']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }
}

