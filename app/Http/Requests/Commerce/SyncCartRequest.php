<?php

declare(strict_types=1);

namespace App\Http\Requests\Commerce;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for Cart Synchronization
 * Validates cart items array from localStorage
 */
class SyncCartRequest extends FormRequest
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
            'items' => ['array'],
            'items.*.type' => ['required', 'string'],
            'items.*.id' => ['required'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
            'items.*.title' => ['nullable', 'string', 'max:255'],
        ];
    }
}

