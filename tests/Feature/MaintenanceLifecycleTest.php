<?php

use App\Enum\MaintenanceStatus;
use App\Enum\VehicleStatus;
use App\Models\MaintenanceOrder;
use App\Models\MaintenanceType;
use App\Models\Mechanic;
use App\Models\SparePart;
use App\Models\Vehicle;
use App\Models\VehicleBrandModel;
use App\Services\MaintenanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('ciclo de vida de una orden de mantenimiento', function () {
    // 1. Setup
    $brandModel = VehicleBrandModel::factory()->create();
    $vehicle = Vehicle::factory()->create(['brand_model_id' => $brandModel->id, 'status' => VehicleStatus::Available, 'mileage' => 5000]);
    $mechanic = Mechanic::factory()->create(['is_active' => true]);
    $type = MaintenanceType::factory()->create(['name' => 'Preventivo']);
    $part = SparePart::factory()->create(['stock' => 10, 'unit_price' => 100.00]);

    $service = new MaintenanceService;

    // 2. Abrir Orden
    $order = $service->openOrder([
        'vehicle_id' => $vehicle->id,
        'mechanic_id' => $mechanic->id,
        'maintenance_type_id' => $type->id,
        'start_date' => now(),
        'mileage_at_service' => 5000,
    ]);

    expect($order->status)->toBe(MaintenanceStatus::PENDING);
    expect($vehicle->refresh()->status)->toBe(VehicleStatus::InMaintenance);

    // 3. Añadir Repuesto
    $service->addPart($order, $part->id, 2);
    expect($part->refresh()->stock)->toBe(8);
    expect($order->refresh()->total_cost)->toBe('200.00');
    expect($order->parts()->count())->toBe(1);

    // 4. Registrar Inspección (Obligatorio para cerrar)
    $order->inspections()->create([
        'category' => 'engine',
        'item_label' => 'Motor Check',
        'status' => \App\Enum\InspectionStatus::GOOD,
    ]);

    // 5. Cerrar Orden
    $service->closeOrder($order, 'Mantenimiento completado con éxito');
    expect($order->refresh()->status)->toBe(MaintenanceStatus::COMPLETED);
    expect($order->end_date)->not->toBeNull();
    expect($vehicle->refresh()->status)->toBe(VehicleStatus::Available);
});

test('no se puede asignar un mecanico ocupado', function () {
    $mechanic = Mechanic::factory()->create(['is_active' => true]);
    $vehicle1 = Vehicle::factory()->create(['status' => VehicleStatus::Available]);
    $vehicle2 = Vehicle::factory()->create(['status' => VehicleStatus::Available]);
    $type = MaintenanceType::factory()->create();

    $service = new MaintenanceService;

    // Abrir primera orden
    $service->openOrder([
        'vehicle_id' => $vehicle1->id,
        'mechanic_id' => $mechanic->id,
        'maintenance_type_id' => $type->id,
    ]);

    // Intentar abrir segunda orden para el mismo mecánico
    expect(fn () => $service->openOrder([
        'vehicle_id' => $vehicle2->id,
        'mechanic_id' => $mechanic->id,
        'maintenance_type_id' => $type->id,
    ]))->toThrow(RuntimeException::class);
});

test('el precio de los repuestos es un snapshot', function () {
    $order = MaintenanceOrder::factory()->create(['status' => MaintenanceStatus::PENDING]);
    $part = SparePart::factory()->create(['stock' => 10, 'unit_price' => 100.00]);

    $service = new MaintenanceService;
    $service->addPart($order, $part->id, 1);

    // Cambiar precio del repuesto original
    $part->update(['unit_price' => 150.00]);

    // El precio en la orden debe seguir siendo 100
    expect($order->parts()->first()->unit_price)->toBe('100.00');
});
