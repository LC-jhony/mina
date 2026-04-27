<?php

namespace Database\Factories;

use App\Models\MaintenanceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceType>
 */
class MaintenanceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->numerify('Tipo Mantenimiento ##'),
            'description' => fake()->sentence(),
            'interval_km' => fake()->randomElement([5000, 10000, 15000, 20000]),
            'interval_days' => fake()->randomElement([90, 180, 365]),
        ];
    }
}
