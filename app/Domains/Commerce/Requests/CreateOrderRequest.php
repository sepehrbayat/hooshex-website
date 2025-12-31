<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Create Order Request
 * Validates data for creating an order from cart
 */
class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_email' => ['nullable', 'email', 'max:255'],
            'billing_phone' => ['nullable', 'string', 'max:20'],
            'billing_address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'billing_name.required' => 'نام صورتحساب الزامی است',
            'billing_email.email' => 'ایمیل معتبر نیست',
        ];
    }
}
