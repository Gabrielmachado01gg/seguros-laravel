<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutenticaChaveApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $chave = (string) config('seguros.api_key');
        $enviada = (string) $request->header('X-API-Key');

        if ($chave === '' || ! hash_equals($chave, $enviada)) {
            return response()->json(['message' => 'Chave de API inválida.'], 401);
        }

        return $next($request);
    }
}
