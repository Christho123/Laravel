<?php

namespace App\Http\Requests\Products\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'in:10,20,50'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
