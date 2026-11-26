<?php

namespace App\Services;

use App\Mail\VencimentoApoliceMail;
use App\Models\Apolice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class NotificadorVencimento
{
    public function __construct(private WhatsAppClient $whatsapp) {}

    /**
     * Avisa (e-mail + WhatsApp) os clientes cujas apólices vencem daqui a $dias dias.
     * Cada canal é avisado uma única vez por apólice. Retorna quantos avisos foram enviados.
     */
    public function notificar(int $dias): int
    {
        $enviados = 0;

        Apolice::with('cliente')->vencendoEm($dias)->each(function (Apolice $apolice) use (&$enviados) {
            $cliente = $apolice->cliente;

            $enviados += (int) $this->enviar($apolice, 'email', function () use ($apolice, $cliente) {
                Mail::to($cliente->email)->send(new VencimentoApoliceMail($apolice));
            });

            if ($cliente->telefone) {
                $enviados += (int) $this->enviar($apolice, 'whatsapp', function () use ($apolice, $cliente) {
                    $this->whatsapp->enviar($cliente->telefone, $this->mensagem($apolice));
                });
            }
        });

        return $enviados;
    }

    private function enviar(Apolice $apolice, string $canal, callable $acao): bool
    {
        $jaEnviado = $apolice->notificacoes()
            ->where('canal', $canal)
            ->where('status', 'enviada')
            ->exists();

        if ($jaEnviado) {
            return false;
        }

        try {
            $acao();
            $apolice->notificacoes()->create(['canal' => $canal, 'status' => 'enviada']);

            return true;
        } catch (Throwable $e) {
            report($e);
            $apolice->notificacoes()->create([
                'canal' => $canal,
                'status' => 'falha',
                'erro' => Str::limit($e->getMessage(), 500),
            ]);

            return false;
        }
    }

    private function mensagem(Apolice $apolice): string
    {
        return sprintf(
            'Olá, %s! Sua apólice %s (%s) vence em %s. Fale com a gente para renovar.',
            $apolice->cliente->nome,
            $apolice->numero,
            $apolice->ramo,
            $apolice->vigencia_fim->format('d/m/Y'),
        );
    }
}
