<?php

namespace App\Filament\Resources\MaintenanceOrders\Pages;

use App\Filament\Resources\MaintenanceOrders\MaintenanceOrderResource;
use App\Models\Vehicle;
use App\Services\MaintenanceService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMaintenanceOrder extends CreateRecord
{
    protected static string $resource = MaintenanceOrderResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $vehicleId = $this->getRecord() !== null
            ? $this->getRecord()->vehicle_id
            : ($this->getQueryStringValue('initialVehicleId') ?? $data['vehicle_id'] ?? null);

        if ($vehicleId && ! isset($data['vehicle_id'])) {
            $vehicle = Vehicle::find($vehicleId);
            if ($vehicle) {
                $data['vehicle_id'] = $vehicle->id;
                $data['mileage_at_service'] = $vehicle->mileage;
            }
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(MaintenanceService::class)->openOrder($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private function getQueryStringValue(string $key): ?string
    {
        return request()->query($key);
    }
}
