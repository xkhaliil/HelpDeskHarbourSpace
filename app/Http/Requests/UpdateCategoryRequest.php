<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Only admins reach this request, the admin middleware already checks that.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // The category may keep its own name, but not take the name of another one.
            'name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('categories', 'name')->ignore($this->route('category'))],
            // Optional: a short text shown on the public category pages.
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
