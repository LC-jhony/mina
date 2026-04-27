<?php

namespace Database\Seeders;

use App\Enum\TripStatus;
use App\Models\Driver;
use App\Models\Mine;
use App\Models\Trip;
use App\Models\TripDriver;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = Vehicle::all();
        $mines = Mine::all();
        $drivers = Driver::where('is_active', true)->get();

        if ($drivers->count() < 4) return;

        // Create 10 realistic trips
        for ($i = 0; $i < 10; $i++) {
            $trip = Trip::factory()->create([
                'vehicle_id' => $vehicles->random()->id,
                'mine_id' => $mines->random()->id,
                'status' => fake()->randomElement([TripStatus::SCHEDULED, TripStatus::IN_PROGRESS, TripStatus::COMPLETED]),
            ]);

            $assignedDrivers = $drivers->random(4);
            
            // Leg Outbound
            TripDriver::create([
                'trip_id' => $trip->id,
                'driver_id' => $assignedDrivers[0]->id,
                'leg' => 'outbound',
                'position' => 1,
            ]);
            TripDriver::create([
                'trip_id' => $trip->id,
                'driver_id' => $assignedDrivers[1]->id,
                'leg' => 'outbound',
                'position' => 2,
            ]);

            // Leg Return
            TripDriver::create([
                'trip_id' => $trip->id,
                'driver_id' => $assignedDrivers[2]->id,
                'leg' => 'return',
                'position' => 1,
            ]);
            TripDriver::create([
                'trip_id' => $trip->id,
                'driver_id' => $assignedDrivers[3]->id,
                'leg' => 'return',
                'position' => 2,
            ]);
        }
    }
}
