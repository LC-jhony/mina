<?php

namespace Database\Factories;

use App\Models\Mechanic;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mechanic>
 */
class MechanicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'specialty_id' => Specialty::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
