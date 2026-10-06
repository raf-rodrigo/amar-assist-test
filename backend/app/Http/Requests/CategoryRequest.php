<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return ['description' => ['required', 'string', 'max:191']];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Informe a descrição.',
            'description.max' => 'A descrição pode ter no máximo 191 caracteres.',
        ];
    }
}
