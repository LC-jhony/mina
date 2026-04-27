<?php

use App\Enum\InspectionStatus;
use App\Enum\MaintenanceStatus;
use App\Enum\VehicleStatus;
use App\Models\MaintenanceType;
use App\Models\Mechanic;
use App\Models\Vehicle;
use App\Services\MaintenanceService;

beforeEach(function () {
    $this->service = app(MaintenanceService::class);
    $this->vehicle = Vehicle::factory()->create(['status' => VehicleStatus::Available]);
    $this->mechanic = Mechanic::factory()->create(['is_active' => true]);
    $this->type = MaintenanceType::factory()->create();

    $this->order = $this->service->openOrder([
        'vehicle_id' => $this->vehicle->id,
        'mechanic_id' => $this->mechanic->id,
        'maintenance_type_id' => $this->type->id,
        'mileage_at_service' => 1000,
    ]);
});

it('cannot complete a maintenance order without at least one inspection item', function () {
    expect($this->order->inspections()->count())->toBe(0);

    // Intentar cerrar sin inspecciones debe lanzar una excepción
    $this->service->closeOrder($this->order);
})->throws(RuntimeException::class, 'No se puede cerrar la orden sin registrar al menos un punto de inspección en el checklist.');

it('can complete a maintenance order with at least one inspection item', function () {
    // Registrar una inspección
    $this->order->inspections()->create([
        'category' => 'fluids',
        'item_label' => 'Nivel de Aceite',
        'value' => '80%',
        'status' => InspectionStatus::GOOD,
    ]);

    $closedOrder = $this->service->closeOrder($this->order);

    expect($closedOrder->status)->toBe(MaintenanceStatus::COMPLETED);
    expect($closedOrder->end_date)->not->toBeNull();
});

it('automatically generates item_key from item_label', function () {
    $inspection = $this->order->inspections()->create([
        'category' => 'filters',
        'item_label' => 'Filtro de Aire Motor',
        'status' => InspectionStatus::WARNING,
    ]);

    expect($inspection->item_key)->toBe('filtro-de-aire-motor');
});

it('validates correct saving of statuses and values', function () {
    $inspection = $this->order->inspections()->create([
        'category' => 'brakes',
        'item_label' => 'Pastilla Frontal Derecha',
        'value' => '5mm',
        'status' => InspectionStatus::DANGER,
        'notes' => 'Cambio inmediato requerido',
    ]);

    expect($inspection->status)->toBe(InspectionStatus::DANGER);
    expect($inspection->value)->toBe('5mm');
    expect($inspection->notes)->toBe('Cambio inmediato requerido');
});
