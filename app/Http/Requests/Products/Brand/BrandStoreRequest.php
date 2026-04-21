<?php

namespace App\Http\Requests\Products\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:brands,name'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}