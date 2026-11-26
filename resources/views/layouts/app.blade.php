<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Painel') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800">
    <header class="bg-slate-900 text-white">
        <nav class="mx-auto flex max-w-5xl items-center gap-6 px-4 py-3">
            <span class="font-semibold">{{ config('app.name') }}</span>
            <a href="{{ route('painel') }}" class="text-sm text-slate-300 hover:text-white">Resumo</a>
            <a href="{{ route('painel.clientes') }}" class="text-sm text-slate-300 hover:text-white">Clientes</a>
            <a href="{{ route('painel.apolices') }}" class="text-sm text-slate-300 hover:text-white">Apólices</a>
        </nav>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-6">
        <h1 class="mb-6 text-2xl font-semibold">@yield('titulo')</h1>
        @yield('conteudo')
    </main>
</body>
</html>
