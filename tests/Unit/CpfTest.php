<?php

namespace Tests\Unit;

use App\Rules\Cpf;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CpfTest extends TestCase
{
    #[DataProvider('cpfsInvalidos')]
    public function test_rejeita_cpf_invalido(string $cpf): void
    {
        $this->assertFalse(Cpf::valido($cpf));
    }

    public static function cpfsInvalidos(): array
    {
        return [
            'vazio' => [''],
            'curto' => ['1234567890'],
            'digitos repetidos' => ['11111111111'],
            'digito verificador errado' => ['52998224726'],
            'letras' => ['abcdefghijk'],
        ];
    }

    public function test_aceita_cpf_valido_com_ou_sem_mascara(): void
    {
        $this->assertTrue(Cpf::valido('52998224725'));
        $this->assertTrue(Cpf::valido('529.982.247-25'));
    }

    public function test_cpf_gerado_e_sempre_valido(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue(Cpf::valido(Cpf::gerar()));
        }
    }
}
