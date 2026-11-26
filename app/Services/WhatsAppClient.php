<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsAppClient
{
    /**
     * Envia uma mensagem de texto pela API REST do provedor configurado.
     *
     * @throws RuntimeException se a integração não estiver configurada
     * @throws RequestException se o provedor responder com erro
     */
    public function enviar(string $telefone, string $mensagem): void
    {
        $url = config('seguros.whatsapp.url');

        if (! $url) {
            throw new RuntimeException('Integração de WhatsApp não configurada.');
        }

        Http::withToken((string) config('seguros.whatsapp.token'))
            ->acceptJson()
            ->timeout(10)
            ->post($url, ['to' => $telefone, 'message' => $mensagem])
            ->throw();
    }
}
