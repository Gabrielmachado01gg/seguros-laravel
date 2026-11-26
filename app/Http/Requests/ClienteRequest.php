<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('cpf')) {
            $this->merge(['cpf' => preg_replace('/\D/', '', (string) $this->input('cpf'))]);
        }

        if ($this->filled('telefone')) {
            $this->merge(['telefone' => preg_replace('/\D/', '', (string) $this->input('telefone'))]);
        }
    }

    public function rules(): array
    {
        $cliente = $this->route('cliente');
        $obrigatorio = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'nome' => [$obrigatorio, 'string', 'max:120'],
            'cpf' => [
                $obrigatorio, 'bail', new Cpf,
                function (string $atributo, mixed $valor, \Closure $falhar) use ($cliente) {
                    $existe = Cliente::where('cpf_hash', Cliente::hashCpf((string) $valor))
                        ->when($cliente, fn ($q) => $q->whereKeyNot($cliente->getKey()))
                        ->exists();

                    if ($existe) {
                        $falhar('Este CPF já está cadastrado.');
                    }
                },
            ],
            'email' => [$obrigatorio, 'email', 'max:150'],
            'telefone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
