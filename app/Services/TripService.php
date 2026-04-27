<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\TripStatus;
use App\Enum\VehicleStatus;
use App\Exceptions\DuplicateDriverAssignmentException;
use App\Exceptions\InvalidLicenseException;
use App\Exceptions\VehicleNotAvailableException;
use App\Jobs\CheckPreventiveMaintenance;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripDriver;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class TripService
{
    /**
     * Crea un viaje con asignación 2+2 de conductores.
     *
     * @param array{
     *   vehicle_id: int,
     *   mine_id: int,
     *   departure_date: string,
     *   origin: string,
     *   observations: ?string,
     *   outbound_driver_1_id: int,
     *   outbound_driver_2_id: int,
     *   return_driver_1_id: int,
     *   return_driver_2_id: int
     * } $data
     */
    public function createTrip(array $data): Trip
    {
        $vehicle = Vehicle::findOrFail($data['vehicle_id']);

        // 1. Verificar disponibilidad del vehículo
        if (! $vehicle->isAvailable()) {
            throw new VehicleNotAvailableException("El vehículo con placa {$vehicle->plate} no está disponible.");
        }

        // 2. Verificar que los 4 conductores sean distintos
        $driverIds = [
            $data['outbound_driver_1_id'],
            $data['outbound_driver_2_id'],
            $data['return_driver_1_id'],
            $data['return_driver_2_id'],
        ];

        if (count(array_unique($driverIds)) !== 4) {
            throw new DuplicateDriverAssignmentException('Se deben asignar 4 conductores distintos para el viaje.');
        }

        // 3. Validar licencias
        $vehicleType = $vehicle->brandModel->vehicle_type;
        foreach ($driverIds as $driverId) {
            $driver = Driver::findOrFail($driverId);
            $this->validateDriverLicense($driver, $vehicleType);
        }

        return DB::transaction(function () use ($data) {
            $trip = Trip::create([
                'vehicle_id' => $data['vehicle_id'],
                'mine_id' => $data['mine_id'],
                'departure_date' => $data['departure_date'],
                'origin' => $data['origin'],
                'observations' => $data['observations'] ?? null,
                'status' => TripStatus::SCHEDULED,
            ]);

            // Asignar conductores de ida
            TripDriver::create([
                'trip_id' => $trip->id,
                'driver_id' => $data['outbound_driver_1_id'],
                'leg' => 'outbound',
                'position' => 1,
            ]);
            TripDriver::create([
                'trip_id' => $trip->id,
                'driver_id' => $data['outbound_driver_2_id'],
                'leg' => 'outbound',
                'position' => 2,
            ]);

            // Asignar conductores de vuelta
            TripDriver::create([
                'trip_id' => $trip->id,
                'driver_id' => $data['return_driver_1_id'],
                'leg' => 'return',
                'position' => 1,
            ]);
            TripDriver::create([
                'trip_id' => $trip->id,
                'driver_id' => $data['return_driver_2_id'],
                'leg' => 'return',
                'position' => 2,
            ]);

            return $trip;
        });
    }

    /**
     * Sincroniza los conductores de un viaje (Regla 2+2).
     *
     * @param array{
     *   outbound_driver_1_id: int,
     *   outbound_driver_2_id: int,
     *   return_driver_1_id: int,
     *   return_driver_2_id: int
     * } $data
     */
    public function syncDrivers(Trip $trip, array $data): void
    {
        $driverIds = [
            $data['outbound_driver_1_id'],
            $data['outbound_driver_2_id'],
            $data['return_driver_1_id'],
            $data['return_driver_2_id'],
        ];

        if (count(array_unique($driverIds)) !== 4) {
            throw new DuplicateDriverAssignmentException('Se deben asignar 4 conductores distintos.');
        }

        $vehicleType = $trip->vehicle->brandModel->vehicle_type;
        foreach ($driverIds as $driverId) {
            $driver = Driver::findOrFail($driverId);
            $this->validateDriverLicense($driver, $vehicleType);
        }

        DB::transaction(function () use ($trip, $data) {
            $trip->tripDrivers()->delete();

            // Asignar Ida
            TripDriver::create(['trip_id' => $trip->id, 'driver_id' => $data['outbound_driver_1_id'], 'leg' => 'outbound', 'position' => 1]);
            TripDriver::create(['trip_id' => $trip->id, 'driver_id' => $data['outbound_driver_2_id'], 'leg' => 'outbound', 'position' => 2]);

            // Asignar Vuelta
            TripDriver::create(['trip_id' => $trip->id, 'driver_id' => $data['return_driver_1_id'], 'leg' => 'return', 'position' => 1]);
            TripDriver::create(['trip_id' => $trip->id, 'driver_id' => $data['return_driver_2_id'], 'leg' => 'return', 'position' => 2]);
        });
    }

    /**
     * Inicia un viaje programado.
     */
    public function startTrip(Trip $trip): Trip
    {
        if ($trip->status !== TripStatus::SCHEDULED) {
            throw new \RuntimeException('Solo se pueden iniciar viajes programados.');
        }

        if (! $trip->vehicle->isAvailable()) {
            throw new VehicleNotAvailableException('El vehículo no está disponible para iniciar el viaje.');
        }

        return DB::transaction(function () use ($trip) {
            $trip->update(['status' => TripStatus::IN_PROGRESS]);
            $trip->vehicle->update(['status' => VehicleStatus::OnTrip]);

            return $trip->fresh();
        });
    }

    /**
     * Completa un viaje en progreso.
     */
    public function completeTrip(Trip $trip, int $finalMileage, ?string $observations = null): Trip
    {
        if ($trip->status !== TripStatus::IN_PROGRESS) {
            throw new \RuntimeException('Solo se pueden completar viajes en progreso.');
        }

        if ($finalMileage < $trip->vehicle->mileage) {
            throw new \InvalidArgumentException("El kilometraje final ({$finalMileage}) no puede ser menor al actual ({$trip->vehicle->mileage}).");
        }

        return DB::transaction(function () use ($trip, $finalMileage, $observations) {
            $trip->update([
                'status' => TripStatus::COMPLETED,
                'return_date' => now(),
                'observations' => $observations ?? $trip->observations,
            ]);

            $trip->vehicle->update([
                'mileage' => $finalMileage,
                'status' => VehicleStatus::Available,
            ]);

            CheckPreventiveMaintenance::dispatch($trip->vehicle);

            return $trip->fresh();
        });
    }

    /**
     * Cancela un viaje programado.
     */
    public function cancelTrip(Trip $trip): Trip
    {
        if ($trip->status !== TripStatus::SCHEDULED) {
            throw new \RuntimeException('Solo se pueden cancelar viajes programados.');
        }

        $trip->update(['status' => TripStatus::CANCELLED]);

        return $trip->fresh();
    }

    /**
     * Valida que el conductor tenga una licencia activa y compatible.
     */
    private function validateDriverLicense(Driver $driver, string $vehicleType): void
    {
        if (! $driver->is_active) {
            throw new InvalidLicenseException("El conductor {$driver->full_name} no está activo.");
        }

        $license = $driver->activeLicenseFor($vehicleType);

        if (! $license) {
            throw new InvalidLicenseException("El conductor {$driver->full_name} no tiene una licencia válida para el tipo de vehículo: {$vehicleType}");
        }
    }
}
