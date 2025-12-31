<?php

declare(strict_types=1);

namespace App\Domains\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Redirect Request
 * Validates data for creating redirects
 */
class StoreRedirectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $redirectId = $this->route('redirect')?->id ?? $this->route('redirect');

        return [
            'source_url' => [
                'required',
                'string',
                'max:500',
                'unique:redirects,source_url' . ($redirectId ? ",{$redirectId}" : ''),
            ],
            'target_url' => ['required', 'string', 'max:500', 'url'],
            'status_code' => ['nullable', 'integer', 'in:301,302,307'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'source_url.required' => 'آدرس مبدا الزامی است',
            'source_url.unique' => 'این آدرس مبدا قبلاً ثبت شده است',
            'target_url.required' => 'آدرس مقصد الزامی است',
            'target_url.url' => 'آدرس مقصد باید یک URL معتبر باشد',
        ];
    }
}
