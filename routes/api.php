<?php

use App\Http\Controllers\Api\ApoliceController;
use App\Http\Controllers\Api\ClienteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.key', 'throttle:60,1'])->group(function () {
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('apolices', ApoliceController::class)
        ->parameters(['apolices' => 'apolice']);
});
