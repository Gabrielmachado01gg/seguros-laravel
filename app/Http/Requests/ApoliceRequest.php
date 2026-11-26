<?php

namespace App\Http\Requests;

use App\Models\Apolice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApoliceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $obrigatorio = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'cliente_id' => [$obrigatorio, 'integer', 'exists:clientes,id'],
            'numero' => [
                $obrigatorio, 'string', 'max:40',
                Rule::unique('apolices', 'numero')->ignore($this->route('apolice')),
            ],
            'ramo' => [$obrigatorio, Rule::in(Apolice::RAMOS)],
            'seguradora' => [$obrigatorio, 'string', 'max:80'],
            'premio' => [$obrigatorio, 'numeric', 'min:0', 'max:99999999.99'],
            'vigencia_inicio' => [$obrigatorio, 'date'],
            'vigencia_fim' => [$obrigatorio, 'date', 'after:vigencia_inicio'],
            'status' => ['sometimes', Rule::in(Apolice::STATUS)],
        ];
    }
}
