<?php

namespace Tests\Feature;

use App\Mail\VencimentoApoliceMail;
use App\Models\Apolice;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AvisoVencimentoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        config([
            'seguros.dias_aviso' => 30,
            'seguros.whatsapp.url' => 'https://whatsapp.test/mensagens',
            'seguros.whatsapp.token' => 'segredo',
        ]);
    }

    public function test_avisa_por_email_e_whatsapp_quem_vence_em_30_dias(): void
    {
        Http::fake(['whatsapp.test/*' => Http::response(['ok' => true])]);

        $cliente = Cliente::factory()->create(['email' => 'cliente@exemplo.com', 'telefone' => '5551999990000']);
        $apolice = Apolice::factory()->vencendoEm(30)->for($cliente)->create();
        Apolice::factory()->vencendoEm(10)->create();                          // fora da janela
        Apolice::factory()->vencendoEm(30)->create(['status' => 'cancelada']); // não está ativa

        $this->artisan('apolices:avisar-vencimento')
            ->expectsOutputToContain('2 aviso(s)')
            ->assertSuccessful();

        Mail::assertSent(VencimentoApoliceMail::class, 1);
        Mail::assertSent(VencimentoApoliceMail::class, fn ($m) => $m->hasTo('cliente@exemplo.com'));

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $r) => $r->url() === 'https://whatsapp.test/mensagens'
            && $r['to'] === '5551999990000'
            && $r->hasHeader('Authorization', 'Bearer segredo')
            && str_contains($r['message'], $apolice->numero));

        $this->assertDatabaseCount('notificacoes', 2);
        $this->assertDatabaseHas('notificacoes', ['apolice_id' => $apolice->id, 'canal' => 'email', 'status' => 'enviada']);
        $this->assertDatabaseHas('notificacoes', ['apolice_id' => $apolice->id, 'canal' => 'whatsapp', 'status' => 'enviada']);
    }

    public function test_nao_avisa_duas_vezes_o_mesmo_cliente(): void
    {
        Http::fake(['whatsapp.test/*' => Http::response(['ok' => true])]);
        Apolice::factory()->vencendoEm(30)->create();

        $this->artisan('apolices:avisar-vencimento')->assertSuccessful();
        $this->artisan('apolices:avisar-vencimento')->expectsOutputToContain('0 aviso(s)')->assertSuccessful();

        Mail::assertSent(VencimentoApoliceMail::class, 1);
        Http::assertSentCount(1);
    }

    public function test_falha_no_whatsapp_e_registrada_sem_impedir_o_email(): void
    {
        Http::fake(['whatsapp.test/*' => Http::response(['erro' => 'indisponivel'], 500)]);
        $apolice = Apolice::factory()->vencendoEm(30)->create();

        $this->artisan('apolices:avisar-vencimento')->assertSuccessful();

        Mail::assertSent(VencimentoApoliceMail::class, 1);
        $this->assertDatabaseHas('notificacoes', ['apolice_id' => $apolice->id, 'canal' => 'email', 'status' => 'enviada']);
        $this->assertDatabaseHas('notificacoes', ['apolice_id' => $apolice->id, 'canal' => 'whatsapp', 'status' => 'falha']);
    }

    public function test_aceita_janela_de_dias_personalizada(): void
    {
        Http::fake();
        Apolice::factory()->vencendoEm(7)->create();

        $this->artisan('apolices:avisar-vencimento --dias=7')->expectsOutputToContain('2 aviso(s)')->assertSuccessful();
    }
}
