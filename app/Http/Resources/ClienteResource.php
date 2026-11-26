<?php

namespace App\Http\Resources;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Cliente */
class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            // O CPF completo nunca sai pela API (minimização de dados - LGPD).
            'cpf' => $this->cpfMascarado(),
            'email' => $this->email,
            'telefone' => $this->telefone,
            'criado_em' => $this->created_at?->toIso8601String(),
        ];
    }
}
