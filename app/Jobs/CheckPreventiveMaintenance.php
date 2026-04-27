<?php

namespace App\Jobs;

use App\Models\MaintenanceOrder;
use App\Models\MaintenanceType;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckPreventiveMaintenance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Vehicle $vehicle,
        public ?MaintenanceType $specificType = null
    ) {}

    public function handle(): void
    {
        $types = $this->specificType
            ? collect([$this->specificType])
            : MaintenanceType::whereNotNull('interval_km')->orWhereNotNull('interval_days')->get();

        foreach ($types as $type) {
            $lastOrder = MaintenanceOrder::where('vehicle_id', $this->vehicle->id)
                ->where('maintenance_type_id', $type->id)
                ->where('status', 'completed')
                ->latest('end_date')
                ->first();

            // ── KM CHECK ──
            if ($type->interval_km) {
                $baseKm = $lastOrder?->mileage_at_service ?? 0;
                $dueKm = $baseKm + $type->interval_km;
                $kmOverdue = $this->vehicle->mileage - $dueKm;

                if ($kmOverdue >= 0) {
                    $this->createAlert('kilómetros', $type, $kmOverdue);
                }
            }

            // ── DAYS CHECK ──
            if ($type->interval_days) {
                $baseDate = $lastOrder?->end_date ?? $this->vehicle->created_at;
                $daysSince = now()->diffInDays($baseDate);
                $daysOverdue = $daysSince - $type->interval_days;

                if ($daysOverdue >= 0) {
                    $this->createAlert('días', $type, (int) $daysOverdue);
                }
            }
        }
    }

    private function createAlert(string $triggerType, MaintenanceType $type, int $overdueAmount): void
    {
        Log::info("Alerta de mantenimiento preventivo: Vehículo {$this->vehicle->plate} requiere {$type->name} por exceso de {$overdueAmount} {$triggerType}.");

        // Notificación en base de datos para coordinadores (ejemplo simplificado)
        Notification::make()
            ->title("Mantenimiento Sugerido: {$this->vehicle->plate}")
            ->body("El vehículo requiere '{$type->name}' (Vencido por {$overdueAmount} {$triggerType}).")
            ->warning()
            ->sendToDatabase(User::all()); // En producción filtrar por rol
    }
}
