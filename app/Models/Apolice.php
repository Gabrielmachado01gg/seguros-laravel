<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apolice extends Model
{
    use HasFactory;

    public const RAMOS = ['auto', 'vida', 'residencial', 'empresarial', 'saude'];

    public const STATUS = ['ativa', 'cancelada', 'vencida'];

    protected $table = 'apolices';

    protected $attributes = ['status' => 'ativa'];

    protected $fillable = [
        'cliente_id', 'numero', 'ramo', 'seguradora', 'premio',
        'vigencia_inicio', 'vigencia_fim', 'status',
    ];

    protected function casts(): array
    {
        return [
            'premio' => 'decimal:2',
            'vigencia_inicio' => 'date',
            'vigencia_fim' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class);
    }

    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('status', 'ativa');
    }

    /** Apólices ativas cuja vigência termina exatamente daqui a $dias dias. */
    public function scopeVencendoEm(Builder $query, int $dias): Builder
    {
        return $query->ativas()->whereDate('vigencia_fim', now()->addDays($dias)->toDateString());
    }
}
