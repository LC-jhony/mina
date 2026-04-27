<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\DriverFactory;
use Database\Factories\LicenseCategoryFactory;
use Database\Factories\LicenseFactory;
use Database\Factories\MaintenanceOrderFactory;
use Database\Factories\MaintenanceOrderPartFactory;
use Database\Factories\MaintenanceTypeFactory;
use Database\Factories\MechanicFactory;
use Database\Factories\MineFactory;
use Database\Factories\SparePartFactory;
use Database\Factories\SpecialtyFactory;
use Database\Factories\TripDriverFactory;
use Database\Factories\TripFactory;
use Database\Factories\VehicleBrandModelFactory;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            InitialSetupSeeder::class,
            InventorySeeder::class,
            FleetSeeder::class,
            TripSeeder::class,
            MaintenanceSeeder::class,
        ]);
        User::factory(4)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
