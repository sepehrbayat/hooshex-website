<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Add To Cart Request
 * Validates data for adding items to cart
 */
class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cart works for guests too
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_type' => ['required', 'string', 'in:course,product'],
            'product_id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_type.required' => 'نوع محصول الزامی است',
            'product_type.in' => 'نوع محصول نامعتبر است',
            'product_id.required' => 'شناسه محصول الزامی است',
            'quantity.min' => 'تعداد باید حداقل ۱ باشد',
            'quantity.max' => 'تعداد نباید بیشتر از ۱۰ باشد',
        ];
    }
}
