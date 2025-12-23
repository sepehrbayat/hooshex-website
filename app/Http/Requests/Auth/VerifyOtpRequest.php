<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for OTP Verification
 * Validates mobile number, OTP code, and optional name
 */
class VerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'regex:/^(\+98|0)?9\d{9}$/'],
            'code' => ['required', 'string', 'size:5'],
            'name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mobile.required' => 'شماره موبایل الزامی است.',
            'mobile.regex' => 'شماره موبایل معتبر نیست.',
            'code.required' => 'کد تایید الزامی است.',
            'code.size' => 'کد تایید باید ۵ رقم باشد.',
        ];
    }
}

