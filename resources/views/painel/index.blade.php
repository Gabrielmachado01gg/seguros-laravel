@extends('layouts.app')

@section('titulo', 'Resumo')

@section('conteudo')
    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Clientes</p>
            <p class="text-3xl font-semibold">{{ $totalClientes }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Apólices ativas</p>
            <p class="text-3xl font-semibold">{{ $totalAtivas }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Prêmio total (ativas)</p>
            <p class="text-3xl font-semibold">R$ {{ number_format($premioAtivas, 2, ',', '.') }}</p>
        </div>
    </div>

    <h2 class="mb-3 text-lg font-semibold">Vencendo nos próximos {{ $dias }} dias</h2>
    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="px-4 py-2">Apólice</th>
                    <th class="px-4 py-2">Cliente</th>
                    <th class="px-4 py-2">Ramo</th>
                    <th class="px-4 py-2">Vence em</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vencendo as $apolice)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $apolice->numero }}</td>
                        <td class="px-4 py-2">{{ $apolice->cliente->nome }}</td>
                        <td class="px-4 py-2 capitalize">{{ $apolice->ramo }}</td>
                        <td class="px-4 py-2">{{ $apolice->vigencia_fim->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">Nenhuma apólice vencendo neste período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
