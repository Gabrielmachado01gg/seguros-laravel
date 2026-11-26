<?php

namespace App\Http\Resources;

use App\Models\Apolice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Apolice */
class ApoliceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'ramo' => $this->ramo,
            'seguradora' => $this->seguradora,
            'premio' => (float) $this->premio,
            'vigencia_inicio' => $this->vigencia_inicio->toDateString(),
            'vigencia_fim' => $this->vigencia_fim->toDateString(),
            'status' => $this->status,
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
        ];
    }
}
