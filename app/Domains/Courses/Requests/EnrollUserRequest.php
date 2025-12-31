<?php

declare(strict_types=1);

namespace App\Domains\Courses\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Enroll User Request
 * Validates data for course enrollment
 */
class EnrollUserRequest extends FormRequest
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
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'شناسه دوره الزامی است',
            'course_id.exists' => 'دوره مورد نظر یافت نشد',
        ];
    }
}
