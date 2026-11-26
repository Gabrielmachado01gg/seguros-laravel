@extends('layouts.app')

@section('titulo', 'Clientes')

@section('conteudo')
    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">CPF</th>
                    <th class="px-4 py-2">E-mail</th>
                    <th class="px-4 py-2">Apólices</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clientes as $cliente)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $cliente->nome }}</td>
                        <td class="px-4 py-2">{{ $cliente->cpfMascarado() }}</td>
                        <td class="px-4 py-2">{{ $cliente->email }}</td>
                        <td class="px-4 py-2">{{ $cliente->apolices_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">Nenhum cliente cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $clientes->links() }}</div>
@endsection
