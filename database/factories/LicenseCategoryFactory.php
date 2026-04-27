<?php

namespace Database\Factories;

use App\Models\LicenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LicenseCategory>
 */
class LicenseCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    private const BASE_CATEGORIES = [
        ['code' => 'A', 'description' => 'Motocicleta hasta 125cc', 'vehicle_type' => 'Motorcycle'],
        ['code' => 'B', 'description' => 'Vehiculo particular hasta 3500kg', 'vehicle_type' => 'Car'],
        ['code' => 'C', 'description' => 'Vehiculo de carga hasta 12000kg', 'vehicle_type' => 'Truck'],
        ['code' => 'D', 'description' => 'Autobus hasta 17 asientos', 'vehicle_type' => 'Bus'],
        ['code' => 'E', 'description' => 'Vehiculo articulado mas de 10000kg', 'vehicle_type' => 'Truck'],
    ];

    public function definition(): array
    {
        $base = fake()->randomElement(self::BASE_CATEGORIES);
        $suffix = fake()->unique()->numberBetween(1, 99);
        $code = $base['code'].$suffix;

        return [
            'code' => $code,
            'description' => $base['description'].' - '.fake()->word(),
            'vehicle_type' => $base['vehicle_type'],
        ];
    }
}
