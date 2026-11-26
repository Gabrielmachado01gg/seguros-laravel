<?php

namespace Tests\Feature\Api;

use App\Models\Apolice;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApoliceApiTest extends TestCase
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

    private function dados(array $extra = []): array
    {
        return array_merge([
            'cliente_id' => Cliente::factory()->create()->id,
            'numero' => 'AP-0001',
            'ramo' => 'auto',
            'seguradora' => 'Porto Seguro',
            'premio' => 1234.5,
            'vigencia_inicio' => '2026-01-01',
            'vigencia_fim' => '2027-01-01',
        ], $extra);
    }

    public function test_cria_apolice(): void
    {
        $this->api()->postJson('/api/apolices', $this->dados())
            ->assertCreated()
            ->assertJsonPath('data.numero', 'AP-0001')
            ->assertJsonPath('data.status', 'ativa')
            ->assertJsonPath('data.premio', 1234.5);
    }

    public function test_valida_vigencia_ramo_e_numero_unico(): void
    {
        $this->api()->postJson('/api/apolices', $this->dados(['vigencia_fim' => '2025-01-01']))
            ->assertUnprocessable()->assertJsonValidationErrors('vigencia_fim');

        $this->api()->postJson('/api/apolices', $this->dados(['ramo' => 'foguete']))
            ->assertUnprocessable()->assertJsonValidationErrors('ramo');

        Apolice::factory()->create(['numero' => 'AP-0001']);
        $this->api()->postJson('/api/apolices', $this->dados())
            ->assertUnprocessable()->assertJsonValidationErrors('numero');
    }

    public function test_filtra_por_status_e_atualiza(): void
    {
        $ativa = Apolice::factory()->create();
        Apolice::factory()->create(['status' => 'cancelada']);

        $this->api()->getJson('/api/apolices?status=ativa')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $ativa->id);

        $this->api()->patchJson("/api/apolices/{$ativa->id}", ['status' => 'cancelada'])
            ->assertOk()->assertJsonPath('data.status', 'cancelada');
    }

    public function test_remove_apolice(): void
    {
        $apolice = Apolice::factory()->create();

        $this->api()->deleteJson("/api/apolices/{$apolice->id}")->assertNoContent();

        $this->assertDatabaseMissing('apolices', ['id' => $apolice->id]);
    }
}
