<?php

namespace App\Console\Commands;

use App\Services\NotificadorVencimento;
use Illuminate\Console\Command;

class AvisarVencimentoApolices extends Command
{
    protected $signature = 'apolices:avisar-vencimento {--dias= : Quantos dias antes do vencimento (padrão: SEGUROS_DIAS_AVISO)}';

    protected $description = 'Avisa por e-mail e WhatsApp os clientes com apólice prestes a vencer';

    public function handle(NotificadorVencimento $notificador): int
    {
        $dias = (int) ($this->option('dias') ?: config('seguros.dias_aviso'));

        $total = $notificador->notificar($dias);

        $this->info("{$total} aviso(s) enviado(s) para apólices que vencem em {$dias} dia(s).");

        return self::SUCCESS;
    }
}
