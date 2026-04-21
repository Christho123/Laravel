<?php

namespace App\Http\Requests\Inventory\Stats;

use Illuminate\Foundation\Http\FormRequest;

class InventoryStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'range' => ['sometimes', 'string', 'in:daily,week,month,3_months,6_months,1_year'],
            'threshold' => ['sometimes', 'integer', 'min:1', 'max:1000000'],
        ];
    }
}
