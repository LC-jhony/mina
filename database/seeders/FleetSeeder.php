<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\License;
use App\Models\LicenseCategory;
use App\Models\Mechanic;
use App\Models\Specialty;
use App\Models\Vehicle;
use App\Models\VehicleBrandModel;
use Illuminate\Database\Seeder;

class FleetSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Brand Models
        $models = [
            ['brand' => 'Toyota', 'model' => 'Hilux', 'vehicle_type' => 'Truck', 'passenger_capacity' => 5],
            ['brand' => 'Toyota', 'model' => 'Land Cruiser', 'vehicle_type' => 'Truck', 'passenger_capacity' => 7],
            ['brand' => 'Mitsubishi', 'model' => 'L200', 'vehicle_type' => 'Truck', 'passenger_capacity' => 5],
            ['brand' => 'Volvo', 'model' => 'FMX', 'vehicle_type' => 'Truck', 'passenger_capacity' => 2],
            ['brand' => 'Mercedes-Benz', 'model' => 'Sprinter', 'vehicle_type' => 'Car', 'passenger_capacity' => 15],
            ['brand' => 'Hyundai', 'model' => 'Santa Fe', 'vehicle_type' => 'Car', 'passenger_capacity' => 7],
        ];

        $brandModels = collect();
        foreach ($models as $model) {
            $brandModels->push(
                VehicleBrandModel::updateOrCreate(
                    ['brand' => $model['brand'], 'model' => $model['model']],
                    $model
                )
            );
        }

        // 2. Vehicles (Recycling the brand models to avoid duplicates)
        Vehicle::factory()->recycle($brandModels)->count(20)->create();

        // 3. Drivers with Licenses
        $categories = LicenseCategory::all();
        
        Driver::factory()->count(15)->create()->each(function ($driver) use ($categories) {
            License::factory()->create([
                'driver_id' => $driver->id,
                'category_id' => $categories->random()->id,
                'status' => 'active',
                'expiry_date' => now()->addYears(2),
            ]);
        });

        // 4. Mechanics
        $specialties = Specialty::all();
        Mechanic::factory()->count(10)->create()->each(function ($mechanic) use ($specialties) {
            $mechanic->specialty_id = $specialties->random()->id;
            $mechanic->save();
        });
    }
}
