<?php

namespace Tests\Feature;

use App\Models\Apolice;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PainelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_painel_exige_login(): void
    {
        $this->get('/')->assertUnauthorized();
        $this->get('/clientes')->assertUnauthorized();
        $this->get('/apolices')->assertUnauthorized();
    }

    public function test_resumo_mostra_totais_e_vencimentos(): void
    {
        Cliente::factory()->count(2)->create();
        $apolice = Apolice::factory()->vencendoEm(15)->create(['numero' => 'AP-VENCE-15']);

        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertSee('AP-VENCE-15')
            ->assertSee($apolice->cliente->nome);
    }

    public function test_lista_de_clientes_mascara_o_cpf(): void
    {
        $cliente = Cliente::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get('/clientes')
            ->assertOk()
            ->assertSee($cliente->nome)
            ->assertSee($cliente->cpfMascarado())
            ->assertDontSee($cliente->cpf);
    }

    public function test_lista_de_apolices(): void
    {
        Apolice::factory()->create(['numero' => 'AP-LISTA-1']);

        $this->actingAs(User::factory()->create())
            ->get('/apolices')
            ->assertOk()
            ->assertSee('AP-LISTA-1');
    }
}
