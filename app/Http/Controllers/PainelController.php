<?php

namespace App\Http\Controllers;

use App\Models\Apolice;
use App\Models\Cliente;
use Illuminate\Contracts\View\View;

class PainelController extends Controller
{
    public function index(): View
    {
        $dias = (int) config('seguros.dias_aviso');

        return view('painel.index', [
            'totalClientes' => Cliente::count(),
            'totalAtivas' => Apolice::ativas()->count(),
            'premioAtivas' => Apolice::ativas()->sum('premio'),
            'dias' => $dias,
            'vencendo' => Apolice::with('cliente')->ativas()
                ->whereBetween('vigencia_fim', [now()->toDateString(), now()->addDays($dias)->toDateString()])
                ->orderBy('vigencia_fim')
                ->limit(10)
                ->get(),
        ]);
    }

    public function clientes(): View
    {
        return view('painel.clientes', [
            'clientes' => Cliente::withCount('apolices')->orderBy('nome')->paginate(15),
        ]);
    }

    public function apolices(): View
    {
        return view('painel.apolices', [
            'apolices' => Apolice::with('cliente')->orderBy('vigencia_fim')->paginate(15),
        ]);
    }
}
