<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\License;
use App\Models\LicenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<License>
 */
class LicenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory(),
            'category_id' => LicenseCategory::factory(),
            'license_number' => fake()->unique()->numerify('###########'),
            'issue_date' => fake()->date('Y-m-d'),
            'expiry_date' => fake()->date('Y-m-d', '+10 years'),
            'status' => 'active',
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
        ]);
    }
}
