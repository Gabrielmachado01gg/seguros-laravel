<?php

namespace Database\Seeders;

use App\Models\Apolice;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuário do painel (HTTP Basic). Defina ADMIN_EMAIL / ADMIN_PASSWORD no .env.
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            ['name' => 'Administrador', 'password' => Hash::make(env('ADMIN_PASSWORD', 'troque-esta-senha'))],
        );

        if (app()->environment('local')) {
            Cliente::factory()->count(8)->create()->each(function (Cliente $cliente) {
                Apolice::factory()->count(2)->for($cliente)->create();
            });
            Apolice::factory()->vencendoEm(30)->create();
        }
    }
}
