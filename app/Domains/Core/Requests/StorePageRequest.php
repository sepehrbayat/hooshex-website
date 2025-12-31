<?php

declare(strict_types=1);

namespace App\Domains\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Page Request
 * Validates data for creating/updating static pages
 */
class StorePageRequest extends FormRequest
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
        $pageId = $this->route('page')?->id ?? $this->route('page');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 
                'string', 
                'max:255',
                'unique:pages,slug' . ($pageId ? ",{$pageId}" : ''),
                'regex:/^[a-z0-9-]+$/',
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content_blocks' => ['nullable', 'array'],
            'template' => ['nullable', 'string', 'max:50'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان صفحه الزامی است',
            'slug.required' => 'نامک صفحه الزامی است',
            'slug.unique' => 'این نامک قبلاً استفاده شده است',
            'slug.regex' => 'نامک فقط می‌تواند شامل حروف کوچک، اعداد و خط تیره باشد',
        ];
    }
}
