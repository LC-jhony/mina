<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\MaintenanceStatus;
use App\Enum\VehicleStatus;
use App\Exceptions\VehicleNotAvailableException;
use App\Jobs\CheckPreventiveMaintenance;
use App\Models\MaintenanceOrder;
use App\Models\MaintenanceOrderPart;
use App\Models\Mechanic;
use App\Models\SparePart;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    /**
     * Abre una nueva orden de mantenimiento.
     */
    public function openOrder(array $data): MaintenanceOrder
    {
        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        $mechanic = Mechanic::findOrFail($data['mechanic_id']);

        // 1. Verificar disponibilidad del vehículo (available o on_trip)
        if (! in_array($vehicle->status, [VehicleStatus::Available, VehicleStatus::OnTrip])) {
            throw new VehicleNotAvailableException("El vehículo con placa {$vehicle->plate} no puede entrar a mantenimiento en su estado actual: {$vehicle->status->getLabel()}.");
        }

        // 2. Verificar disponibilidad del mecánico (max 1 orden activa)
        if ($mechanic->hasActiveOrder()) {
            throw new \RuntimeException("El mecánico {$mechanic->full_name} ya tiene una orden de mantenimiento activa.");
        }

        return DB::transaction(function () use ($data, $vehicle) {
            $order = MaintenanceOrder::create([
                'vehicle_id' => $data['vehicle_id'],
                'trip_id' => $data['trip_id'] ?? null,
                'mechanic_id' => $data['mechanic_id'],
                'maintenance_type_id' => $data['maintenance_type_id'],
                'start_date' => $data['start_date'] ?? now(),
                'mileage_at_service' => $data['mileage_at_service'] ?? $vehicle->mileage,
                'description' => $data['description'] ?? null,
                'status' => MaintenanceStatus::PENDING,
                'total_cost' => 0.00,
            ]);

            $vehicle->update(['status' => VehicleStatus::InMaintenance]);

            return $order;
        });
    }

    /**
     * Añade un repuesto a la orden con snapshot de precio.
     */
    public function addPart(MaintenanceOrder $order, int $sparePartId, int $qty): MaintenanceOrderPart
    {
        if (! $order->isOpen()) {
            throw new \RuntimeException('No se pueden añadir repuestos a una orden cerrada.');
        }

        return DB::transaction(function () use ($order, $sparePartId, $qty) {
            $part = SparePart::lockForUpdate()->findOrFail($sparePartId);

            if ($part->stock < $qty) {
                throw new \RuntimeException("Stock insuficiente para el repuesto: {$part->name} (Disponible: {$part->stock})");
            }

            $unitPrice = $part->unit_price;
            $subtotal = $unitPrice * $qty;

            $orderPart = $order->parts()->create([
                'spare_part_id' => $sparePartId,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ]);

            $part->decrement('stock', $qty);
            $order->increment('total_cost', $subtotal);

            return $orderPart;
        });
    }

    /**
     * Elimina un repuesto de la orden y restaura el stock.
     */
    public function removePart(MaintenanceOrderPart $line): void
    {
        if (! $line->maintenanceOrder->isOpen()) {
            throw new \RuntimeException('No se pueden eliminar repuestos de una orden cerrada.');
        }

        DB::transaction(function () use ($line) {
            $line->sparePart->increment('stock', $line->quantity);
            $line->maintenanceOrder->decrement('total_cost', $line->subtotal);
            $line->delete();
        });
    }

    /**
     * Cierra la orden de mantenimiento.
     */
    public function closeOrder(MaintenanceOrder $order, ?string $notes = null): MaintenanceOrder
    {
        if (! $order->isOpen()) {
            throw new \RuntimeException('La orden ya está cerrada.');
        }

        // Validar que se hayan registrado inspecciones (checklist)
        if ($order->inspections()->count() === 0) {
            throw new \RuntimeException('No se puede cerrar la orden sin registrar al menos un punto de inspección en el checklist.');
        }

        return DB::transaction(function () use ($order, $notes) {
            $order->update([
                'status' => MaintenanceStatus::COMPLETED,
                'end_date' => now(),
                'description' => $notes ?? $order->description,
            ]);

            $order->vehicle->update(['status' => VehicleStatus::Available]);

            CheckPreventiveMaintenance::dispatch($order->vehicle, $order->maintenanceType);

            return $order->fresh();
        });
    }

    /**
     * Cancela la orden de mantenimiento.
     */
    public function cancelOrder(MaintenanceOrder $order): MaintenanceOrder
    {
        if ($order->status !== MaintenanceStatus::PENDING) {
            throw new \RuntimeException('Solo se pueden cancelar órdenes en estado pendiente.');
        }

        return DB::transaction(function () use ($order) {
            // Restaurar stock de todos los repuestos
            foreach ($order->parts as $line) {
                $this->removePart($line);
            }

            $order->update(['status' => MaintenanceStatus::CANCELLED]);
            $order->vehicle->update(['status' => VehicleStatus::Available]);

            return $order->fresh();
        });
    }
}
