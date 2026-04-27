<?php

namespace Database\Seeders;

use App\Enum\InspectionStatus;
use App\Enum\MaintenanceStatus;
use App\Models\MaintenanceOrder;
use App\Models\MaintenanceType;
use App\Models\Mechanic;
use App\Models\SparePart;
use App\Models\Vehicle;
use App\Services\MaintenanceService;
use Illuminate\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = Vehicle::all();
        $mechanics = Mechanic::where('is_active', true)->get();
        $types = MaintenanceType::all();
        $parts = SparePart::all();
        $service = new MaintenanceService();

        // 1. Create some COMPLETED orders (History)
        for ($i = 0; $i < 5; $i++) {
            $vehicle = $vehicles->random();
            $mechanic = $mechanics->random();
            
            $order = MaintenanceOrder::factory()->create([
                'vehicle_id' => $vehicle->id,
                'mechanic_id' => $mechanic->id,
                'maintenance_type_id' => $types->random()->id,
                'status' => MaintenanceStatus::COMPLETED,
                'start_date' => now()->subDays(rand(10, 30)),
                'end_date' => now()->subDays(rand(1, 9)),
            ]);

            // Add some parts
            for ($j = 0; $j < rand(1, 3); $j++) {
                $part = $parts->random();
                $qty = rand(1, 2);
                $order->parts()->create([
                    'spare_part_id' => $part->id,
                    'quantity' => $qty,
                    'unit_price' => $part->unit_price,
                    'subtotal' => $part->unit_price * $qty,
                ]);
                $order->increment('total_cost', $part->unit_price * $qty);
            }

            // Add inspections (Providing item_key explicitly due to DB strict mode)
            $categories = ['fluids', 'filters', 'brakes', 'tires', 'engine'];
            foreach ($categories as $cat) {
                $label = ucfirst($cat) . ' Check';
                $order->inspections()->create([
                    'category' => $cat,
                    'item_label' => $label,
                    'item_key' => str($label)->slug()->toString(),
                    'status' => InspectionStatus::GOOD,
                ]);
            }
        }

        // 2. Create some PENDING orders (Current work)
        $availableMechanics = $mechanics->filter(fn($m) => !$m->hasActiveOrder());
        
        foreach ($availableMechanics->take(3) as $mechanic) {
            $vehicle = $vehicles->where('status', \App\Enum\VehicleStatus::Available)->random();
            
            try {
                $order = $service->openOrder([
                    'vehicle_id' => $vehicle->id,
                    'mechanic_id' => $mechanic->id,
                    'maintenance_type_id' => $types->random()->id,
                ]);

                // Add one part to some
                if (rand(0, 1)) {
                    $service->addPart($order, $parts->random()->id, 1);
                }

                // Add one inspection
                $label = 'Initial Inspection';
                $order->inspections()->create([
                    'category' => 'general',
                    'item_label' => $label,
                    'item_key' => str($label)->slug()->toString(),
                    'status' => InspectionStatus::WARNING,
                    'notes' => 'Requiere revisión detallada',
                ]);
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}
