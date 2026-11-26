<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Rules\Cpf;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Cliente> */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cpf' => Cpf::gerar(),
            'email' => fake()->unique()->safeEmail(),
            'telefone' => '5551'.fake()->numerify('9########'),
        ];
    }
}
