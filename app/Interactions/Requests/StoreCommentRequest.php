<?php

declare(strict_types=1);

namespace App\Interactions\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Comment Request
 * Validates data for creating a comment
 */
class StoreCommentRequest extends FormRequest
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
            'body' => ['required', 'string', 'min:10', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'body.required' => 'متن نظر الزامی است',
            'body.min' => 'متن نظر باید حداقل ۱۰ کاراکتر باشد',
            'body.max' => 'متن نظر نباید بیشتر از ۲۰۰۰ کاراکتر باشد',
            'parent_id.exists' => 'نظر والد یافت نشد',
        ];
    }
}
