<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripDriver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TripDriver>
 */
class TripDriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::all()->random()->id,
            'driver_id' => Driver::all()->random()->id,
            'leg' => fake()->randomElement(['outbound', 'return']),
            'position' => fake()->randomElement([1, 2]),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 1,
        ]);
    }

    public function secondary(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 2,
        ]);
    }
}
