<?php

return [

    /*
    | Chave exigida no cabeçalho X-API-Key para usar a API REST.
    */
    'api_key' => env('SEGUROS_API_KEY'),

    /*
    | Quantos dias antes do fim da vigência o cliente é avisado.
    */
    'dias_aviso' => (int) env('SEGUROS_DIAS_AVISO', 30),

    /*
    | Integração com o provedor de WhatsApp (API REST).
    */
    'whatsapp' => [
        'url' => env('WHATSAPP_API_URL'),
        'token' => env('WHATSAPP_API_TOKEN'),
    ],

];
