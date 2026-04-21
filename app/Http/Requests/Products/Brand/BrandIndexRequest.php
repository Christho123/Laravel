<?php

namespace App\Http\Requests\Products\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'page_size' => ['sometimes', 'integer', 'in:10,20,50'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}