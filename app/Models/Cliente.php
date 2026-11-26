<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = ['nome', 'cpf', 'email', 'telefone'];

    protected $hidden = ['cpf', 'cpf_hash'];

    protected function casts(): array
    {
        return ['cpf' => 'encrypted'];
    }

    protected static function booted(): void
    {
        static::saving(function (Cliente $cliente) {
            if ($cliente->isDirty('cpf')) {
                $cliente->cpf_hash = self::hashCpf($cliente->cpf);
            }
        });
    }

    public static function hashCpf(string $cpf): string
    {
        return hash_hmac('sha256', preg_replace('/\D/', '', $cpf), (string) config('app.key'));
    }

    public function cpfMascarado(): string
    {
        $digitos = preg_replace('/\D/', '', (string) $this->cpf);

        return '***.'.substr($digitos, 3, 3).'.'.substr($digitos, 6, 3).'-**';
    }

    public function apolices(): HasMany
    {
        return $this->hasMany(Apolice::class);
    }
}
