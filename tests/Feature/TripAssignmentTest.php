<?php

use App\Enum\TripStatus;
use App\Enum\VehicleStatus;
use App\Exceptions\DuplicateDriverAssignmentException;
use App\Exceptions\VehicleNotAvailableException;
use App\Models\Driver;
use App\Models\License;
use App\Models\LicenseCategory;
use App\Models\Mine;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\VehicleBrandModel;
use App\Services\TripService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('un viaje requiere 2 choferes de ida y 2 de vuelta con licencias validas', function () {
    // Setup
    $mine = Mine::factory()->create(['is_active' => true]);
    $brandModel = VehicleBrandModel::factory()->create(['vehicle_type' => 'A-III']);
    $vehicle = Vehicle::factory()->create(['brand_model_id' => $brandModel->id, 'status' => VehicleStatus::Available]);

    $category = LicenseCategory::factory()->create(['vehicle_type' => 'A-III']);

    $drivers = [];
    for ($i = 0; $i < 4; $i++) {
        $driver = Driver::factory()->create(['is_active' => true]);
        License::factory()->create([
            'driver_id' => $driver->id,
            'category_id' => $category->id,
            'status' => 'active',
            'expiry_date' => now()->addYear(),
        ]);
        $drivers[] = $driver;
    }

    $data = [
        'vehicle_id' => $vehicle->id,
        'mine_id' => $mine->id,
        'departure_date' => now()->toDateTimeString(),
        'origin' => 'Base Central',
        'outbound_driver_1_id' => $drivers[0]->id,
        'outbound_driver_2_id' => $drivers[1]->id,
        'return_driver_1_id' => $drivers[2]->id,
        'return_driver_2_id' => $drivers[3]->id,
    ];

    // Action
    $service = new TripService;
    $trip = $service->createTrip($data);

    // Assertions
    expect($trip->tripDrivers()->count())->toBe(4);
    expect($trip->outboundDrivers()->count())->toBe(2);
    expect($trip->returnDrivers()->count())->toBe(2);
    expect($trip->status)->toBe(TripStatus::SCHEDULED);
});

test('no se puede crear un viaje con conductores duplicados', function () {
    $mine = Mine::factory()->create();
    $vehicle = Vehicle::factory()->create(['status' => VehicleStatus::Available]);
    $driver = Driver::factory()->create(['is_active' => true]);

    $data = [
        'vehicle_id' => $vehicle->id,
        'mine_id' => $mine->id,
        'departure_date' => now()->toDateTimeString(),
        'origin' => 'Base Central',
        'outbound_driver_1_id' => $driver->id,
        'outbound_driver_2_id' => $driver->id, // Duplicado
        'return_driver_1_id' => $driver->id,
        'return_driver_2_id' => $driver->id,
    ];

    $service = new TripService;
    expect(fn () => $service->createTrip($data))->toThrow(DuplicateDriverAssignmentException::class);
});

test('no se puede iniciar un viaje si el vehiculo no esta disponible', function () {
    $trip = Trip::factory()->create(['status' => TripStatus::SCHEDULED]);
    $trip->vehicle->update(['status' => VehicleStatus::InMaintenance]);

    $service = new TripService;
    expect(fn () => $service->startTrip($trip))->toThrow(VehicleNotAvailableException::class);
});

test('ciclo de vida completo del viaje', function () {
    // 1. Crear
    $mine = Mine::factory()->create(['is_active' => true]);
    $brandModel = VehicleBrandModel::factory()->create(['vehicle_type' => 'A-III']);
    $vehicle = Vehicle::factory()->create(['brand_model_id' => $brandModel->id, 'status' => VehicleStatus::Available, 'mileage' => 1000]);
    $category = LicenseCategory::factory()->create(['vehicle_type' => 'A-III']);

    $drivers = Driver::factory()->count(4)->create(['is_active' => true]);
    foreach ($drivers as $d) {
        License::factory()->create(['driver_id' => $d->id, 'category_id' => $category->id, 'status' => 'active', 'expiry_date' => now()->addYear()]);
    }

    $service = new TripService;
    $trip = $service->createTrip([
        'vehicle_id' => $vehicle->id,
        'mine_id' => $mine->id,
        'departure_date' => now()->toDateTimeString(),
        'origin' => 'Base Central',
        'outbound_driver_1_id' => $drivers[0]->id,
        'outbound_driver_2_id' => $drivers[1]->id,
        'return_driver_1_id' => $drivers[2]->id,
        'return_driver_2_id' => $drivers[3]->id,
    ]);

    // 2. Iniciar
    $service->startTrip($trip);
    $trip->refresh();
    expect($trip->status)->toBe(TripStatus::IN_PROGRESS);
    expect($trip->vehicle->status)->toBe(VehicleStatus::OnTrip);

    // 3. Completar
    $service->completeTrip($trip, 1500, 'Llegada sin novedades');
    $trip->refresh();
    expect($trip->status)->toBe(TripStatus::COMPLETED);
    expect($trip->vehicle->status)->toBe(VehicleStatus::Available);
    expect($trip->vehicle->mileage)->toBe(1500);
    expect($trip->return_date)->not->toBeNull();
});

test('no se puede completar un viaje con kilometraje menor al inicial', function () {
    $vehicle = Vehicle::factory()->create(['status' => VehicleStatus::OnTrip, 'mileage' => 1000]);
    $trip = Trip::factory()->create(['status' => TripStatus::IN_PROGRESS, 'vehicle_id' => $vehicle->id]);

    $service = new TripService;
    expect(fn () => $service->completeTrip($trip, 900))->toThrow(InvalidArgumentException::class);
});

test('se puede cancelar un viaje programado', function () {
    $trip = Trip::factory()->create(['status' => TripStatus::SCHEDULED]);

    $service = new TripService;
    $service->cancelTrip($trip);

    expect($trip->refresh()->status)->toBe(TripStatus::CANCELLED);
});
