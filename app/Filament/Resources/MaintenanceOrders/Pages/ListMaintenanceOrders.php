<?php

namespace App\Filament\Resources\MaintenanceOrders\Pages;

use App\Filament\Resources\MaintenanceOrders\MaintenanceOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListMaintenanceOrders extends ListRecords
{
    protected static string $resource = MaintenanceOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon(Heroicon::SquaresPlus),
        ];
    }
}
