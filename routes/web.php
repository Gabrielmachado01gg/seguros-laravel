<?php

use App\Http\Controllers\PainelController;
use Illuminate\Support\Facades\Route;

// Painel interno: protegido por HTTP Basic (usuário da tabela users).
Route::middleware('auth.basic')->group(function () {
    Route::get('/', [PainelController::class, 'index'])->name('painel');
    Route::get('/clientes', [PainelController::class, 'clientes'])->name('painel.clientes');
    Route::get('/apolices', [PainelController::class, 'apolices'])->name('painel.apolices');
});
