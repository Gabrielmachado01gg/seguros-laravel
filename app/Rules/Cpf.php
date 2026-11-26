<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::valido((string) $value)) {
            $fail('O campo :attribute não é um CPF válido.');
        }
    }

    public static function valido(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        return self::digito(substr($cpf, 0, 9)) === (int) $cpf[9]
            && self::digito(substr($cpf, 0, 10)) === (int) $cpf[10];
    }

    /** Gera um CPF válido (usado em factories e testes). */
    public static function gerar(): string
    {
        $base = '';
        for ($i = 0; $i < 9; $i++) {
            $base .= random_int(0, 9);
        }

        if (preg_match('/^(\d)\1{8}$/', $base)) {
            return self::gerar();
        }

        $base .= self::digito($base);

        return $base.self::digito($base);
    }

    private static function digito(string $digitos): int
    {
        $soma = 0;
        $peso = strlen($digitos) + 1;

        foreach (str_split($digitos) as $n) {
            $soma += (int) $n * $peso--;
        }

        return ((10 * $soma) % 11) % 10;
    }
}
