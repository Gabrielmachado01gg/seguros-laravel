<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacao extends Model
{
    protected $table = 'notificacoes';

    protected $fillable = ['apolice_id', 'canal', 'status', 'erro'];

    public function apolice(): BelongsTo
    {
        return $this->belongsTo(Apolice::class);
    }
}
