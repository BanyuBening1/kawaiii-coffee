<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class BestSellersRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'period' => ['required', 'string', 'in:daily,weekly,monthly,yearly'],
            'date'   => ['nullable', 'date_format:Y-m-d'],
            'limit'  => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}