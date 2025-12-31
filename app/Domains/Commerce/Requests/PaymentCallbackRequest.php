<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Payment Callback Request
 * Validates data from payment gateway callback
 */
class PaymentCallbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Callback comes from gateway
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'Authority' => ['nullable', 'string'], // Zarinpal
            'Status' => ['nullable', 'string'],    // Zarinpal
            'order_id' => ['nullable', 'integer'], // Custom tracking
        ];
    }
}
