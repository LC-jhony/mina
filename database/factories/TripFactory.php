<?php

namespace Database\Factories;

use App\Enum\TripStatus;
use App\Models\Mine;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'mine_id' => Mine::factory(),
            'departure_date' => fake()->dateTimeBetween('now', '+1 week'),
            'return_date' => null,
            'origin' => fake()->city(),
            'observations' => fake()->optional()->sentence(),
            'status' => TripStatus::SCHEDULED,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TripStatus::IN_PROGRESS,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TripStatus::COMPLETED,
            'return_date' => fake()->dateTimeBetween($attributes['departure_date'] ?? 'now', '+2 days'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TripStatus::CANCELLED,
        ]);
    }
}
