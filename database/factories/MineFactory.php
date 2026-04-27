<?php

namespace Database\Factories;

use App\Models\Mine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mine>
 */
class MineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Mine',
            'region' => fake()->randomElement(['Norte', 'Sur', 'Este', 'Oeste', 'Centro']),
            'company' => fake()->company(),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'is_active' => true,
        ];
    }
}
