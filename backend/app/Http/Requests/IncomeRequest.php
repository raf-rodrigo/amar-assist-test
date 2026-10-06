<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncomeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('user_id', $this->user()->id)],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.now()->endOfMonth()->toDateString()],
            'description' => ['required', 'string', 'max:191'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Selecione uma categoria.',
            'category_id.exists' => 'A categoria selecionada é inválida.',
            'date.required' => 'Informe a data.',
            'date.date_format' => 'Informe uma data válida.',
            'date.after_or_equal' => 'A data não pode estar no passado.',
            'date.before_or_equal' => 'A receita deve estar dentro do mês atual.',
            'description.required' => 'Informe a descrição.',
            'description.max' => 'A descrição pode ter no máximo 191 caracteres.',
            'amount.required' => 'Informe o valor.',
            'amount.numeric' => 'Informe um valor válido.',
            'amount.min' => 'O valor não pode ser negativo.',
        ];
    }
}
