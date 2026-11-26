<?php

namespace Database\Factories;

use App\Models\Apolice;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Apolice> */
class ApoliceFactory extends Factory
{
    protected $model = Apolice::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'numero' => fake()->unique()->numerify('AP-########'),
            'ramo' => fake()->randomElement(Apolice::RAMOS),
            'seguradora' => fake()->randomElement(['Porto Seguro', 'Sabemi', 'Tokio Marine', 'Allianz', 'Bradesco Seguros']),
            'premio' => fake()->randomFloat(2, 300, 8000),
            'vigencia_inicio' => now()->subMonths(6)->toDateString(),
            'vigencia_fim' => now()->addMonths(6)->toDateString(),
            'status' => 'ativa',
        ];
    }

    public function vencendoEm(int $dias): static
    {
        return $this->state(fn () => [
            'vigencia_inicio' => now()->addDays($dias)->subYear()->toDateString(),
            'vigencia_fim' => now()->addDays($dias)->toDateString(),
            'status' => 'ativa',
        ]);
    }
}
