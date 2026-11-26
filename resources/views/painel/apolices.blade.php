@extends('layouts.app')

@section('titulo', 'Apólices')

@section('conteudo')
    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="px-4 py-2">Número</th>
                    <th class="px-4 py-2">Cliente</th>
                    <th class="px-4 py-2">Seguradora</th>
                    <th class="px-4 py-2">Prêmio</th>
                    <th class="px-4 py-2">Vigência até</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($apolices as $apolice)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $apolice->numero }}</td>
                        <td class="px-4 py-2">{{ $apolice->cliente->nome }}</td>
                        <td class="px-4 py-2">{{ $apolice->seguradora }}</td>
                        <td class="px-4 py-2">R$ {{ number_format($apolice->premio, 2, ',', '.') }}</td>
                        <td class="px-4 py-2">{{ $apolice->vigencia_fim->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 capitalize">{{ $apolice->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-500">Nenhuma apólice cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $apolices->links() }}</div>
@endsection
