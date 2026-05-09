<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class SalesSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ganti dengan auth logic kamu
    }

    public function rules(): array
    {
        return [
            'period' => ['required', 'string', 'in:daily,weekly,monthly,yearly'],
            'date'   => ['nullable', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'period.required' => 'Parameter period wajib diisi.',
            'period.in'       => 'Period harus salah satu dari: daily, weekly, monthly, yearly.',
            'date.date_format' => 'Format tanggal harus Y-m-d (contoh: 2024-01-15).',
        ];
    }
}