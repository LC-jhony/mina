<?php

namespace Database\Factories;

use App\Enum\VehicleStatus;
use App\Models\Vehicle;
use App\Models\VehicleBrandModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_model_id' => VehicleBrandModel::inRandomOrder()->first()?->id ?? VehicleBrandModel::factory(),
            'plate' => strtoupper(fake()->unique()->bothify('???-###')),
            'year' => fake()->numberBetween(2015, 2024),
            'mileage' => fake()->numberBetween(0, 150000),
            'status' => VehicleStatus::Available,
        ];
    }

    public function onTrip(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VehicleStatus::OnTrip,
        ]);
    }

    public function inMaintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VehicleStatus::InMaintenance,
        ]);
    }

    public function outOfService(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VehicleStatus::OutOfService,
        ]);
    }
}
