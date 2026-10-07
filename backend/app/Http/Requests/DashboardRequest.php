<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => ['nullable', 'date_format:Y-m'],
        ];
    }

    public function messages(): array
    {
        return [
            'month.date_format' => 'Informe um mês válido no formato AAAA-MM.',
        ];
    }
}
