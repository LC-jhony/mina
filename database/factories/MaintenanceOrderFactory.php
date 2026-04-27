<?php

namespace Database\Factories;

use App\Enum\MaintenanceStatus;
use App\Models\MaintenanceOrder;
use App\Models\MaintenanceType;
use App\Models\Mechanic;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceOrder>
 */
class MaintenanceOrderFactory extends Factory
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
            'trip_id' => null,
            'mechanic_id' => Mechanic::factory(),
            'maintenance_type_id' => MaintenanceType::factory(),
            'start_date' => now(),
            'end_date' => null,
            'mileage_at_service' => fake()->numberBetween(10000, 100000),
            'description' => fake()->optional()->sentence(),
            'status' => MaintenanceStatus::PENDING,
            'total_cost' => 0.00,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaintenanceStatus::IN_PROGRESS,
        ]);
    }

    public function completed(float $cost = 0.00): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaintenanceStatus::COMPLETED,
            'end_date' => now()->addHours(fake()->numberBetween(2, 8)),
            'total_cost' => $cost > 0 ? $cost : fake()->randomFloat(2, 100, 5000),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaintenanceStatus::CANCELLED,
        ]);
    }
}
