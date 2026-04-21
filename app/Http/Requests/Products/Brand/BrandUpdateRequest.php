<?php

namespace App\Http\Requests\Products\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brand = $this->route('brand');
        $brandId = is_object($brand) ? $brand->getKey() : $brand;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('brands', 'name')->ignore($brandId)],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}