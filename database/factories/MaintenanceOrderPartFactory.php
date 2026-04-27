<?php

namespace Database\Factories;

use App\Models\MaintenanceOrder;
use App\Models\MaintenanceOrderPart;
use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceOrderPart>
 */
class MaintenanceOrderPartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 10, 500);

        return [
            'maintenance_order_id' => MaintenanceOrder::all()->random()->id,
            'spare_part_id' => SparePart::all()->random()->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
        ];
    }
}
