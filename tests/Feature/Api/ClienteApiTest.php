<?php

namespace Tests\Feature\Api;

use App\Models\Cliente;
use App\Rules\Cpf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClienteApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['seguros.api_key' => 'chave-de-teste']);
    }

    private function api(): static
    {
        return $this->withHeader('X-API-Key', 'chave-de-teste');
    }

    public function test_exige_chave_de_api(): void
    {
        $this->getJson('/api/clientes')->assertUnauthorized();
        $this->withHeader('X-API-Key', 'errada')->getJson('/api/clientes')->assertUnauthorized();
    }

    public function test_cria_cliente_e_nao_expoe_cpf_completo(): void
    {
        $cpf = Cpf::gerar();

        $resposta = $this->api()->postJson('/api/clientes', [
            'nome' => 'Maria Souza',
            'cpf' => $cpf,
            'email' => 'maria@exemplo.com',
            'telefone' => '(51) 99999-0000',
        ]);

        $resposta->assertCreated()
            ->assertJsonPath('data.nome', 'Maria Souza')
            ->assertJsonPath('data.telefone', '51999990000');

        $this->assertStringNotContainsString($cpf, $resposta->getContent());
        $this->assertStringEndsWith('-**', $resposta->json('data.cpf'));
    }

    public function test_cpf_fica_criptografado_no_banco(): void
    {
        $cpf = Cpf::gerar();
        $cliente = Cliente::factory()->create(['cpf' => $cpf]);

        $bruto = DB::table('clientes')->where('id', $cliente->id)->value('cpf');

        $this->assertNotSame($cpf, $bruto);
        $this->assertSame($cpf, $cliente->fresh()->cpf);
    }

    public function test_rejeita_cpf_invalido_e_duplicado(): void
    {
        $existente = Cliente::factory()->create();

        $this->api()->postJson('/api/clientes', [
            'nome' => 'Fulano', 'cpf' => '111.111.111-11', 'email' => 'f@exemplo.com',
        ])->assertUnprocessable()->assertJsonValidationErrors('cpf');

        $this->api()->postJson('/api/clientes', [
            'nome' => 'Fulano', 'cpf' => $existente->cpf, 'email' => 'f@exemplo.com',
        ])->assertUnprocessable()->assertJsonValidationErrors('cpf');
    }

    public function test_lista_mostra_e_atualiza_cliente(): void
    {
        $cliente = Cliente::factory()->create(['nome' => 'Ana']);

        $this->api()->getJson('/api/clientes')->assertOk()->assertJsonCount(1, 'data');
        $this->api()->getJson("/api/clientes/{$cliente->id}")->assertOk()->assertJsonPath('data.nome', 'Ana');

        // Atualizar mantendo o próprio CPF não conta como duplicado.
        $this->api()->putJson("/api/clientes/{$cliente->id}", [
            'nome' => 'Ana Paula', 'cpf' => $cliente->cpf,
        ])->assertOk()->assertJsonPath('data.nome', 'Ana Paula');
    }

    public function test_remove_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $this->api()->deleteJson("/api/clientes/{$cliente->id}")->assertNoContent();

        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
    }
}
