<?php

namespace Database\Factories;

use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SparePart>
 */
class SparePartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('SP-####')),
            'name' => fake()->randomElement([
                'Filtro de aceite',
                'Filtro de aire',
                'Filtro de combustible',
                'Bujía',
                'Pastillas de freno',
                'Discos de freno',
                'Batería',
                'Correa de transmisión',
                'Bomba de agua',
                'Termostato',
            ]),
            'description' => fake()->optional()->sentence(),
            'unit' => fake()->randomElement(['piece', 'set', 'liter', 'kg']),
            'unit_price' => fake()->randomFloat(2, 10, 500),
            'stock' => fake()->numberBetween(0, 50),
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
