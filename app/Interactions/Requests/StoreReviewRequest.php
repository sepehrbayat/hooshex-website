<?php

declare(strict_types=1);

namespace App\Interactions\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Review Request
 * Validates data for creating a review
 */
class StoreReviewRequest extends FormRequest
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
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'امتیاز الزامی است',
            'rating.min' => 'امتیاز باید حداقل ۱ باشد',
            'rating.max' => 'امتیاز باید حداکثر ۵ باشد',
            'title.max' => 'عنوان نباید بیشتر از ۲۵۵ کاراکتر باشد',
            'body.max' => 'متن نباید بیشتر از ۲۰۰۰ کاراکتر باشد',
        ];
    }
}
