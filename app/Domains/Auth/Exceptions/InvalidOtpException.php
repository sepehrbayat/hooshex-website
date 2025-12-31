<?php

declare(strict_types=1);

namespace App\Domains\Auth\Exceptions;

use Exception;

/**
 * Exception thrown when OTP verification fails
 */
class InvalidOtpException extends Exception
{
    public function __construct(
        string $message = 'Invalid or expired OTP code',
        int $code = 422
    ) {
        parent::__construct($message, $code);
    }

    /**
     * Render the exception as an HTTP response
     */
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'ok' => false,
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
