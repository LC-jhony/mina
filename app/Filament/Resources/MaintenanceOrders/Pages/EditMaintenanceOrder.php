<?php

namespace App\Filament\Resources\MaintenanceOrders\Pages;

use App\Filament\Resources\MaintenanceOrders\MaintenanceOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceOrder extends EditRecord
{
    protected static string $resource = MaintenanceOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
