<?php

namespace App\Filament\Resources\MaintenanceTypes\Pages;

use App\Filament\Resources\MaintenanceTypes\MaintenanceTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListMaintenanceTypes extends ListRecords
{
    protected static string $resource = MaintenanceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon(Heroicon::SquaresPlus),
        ];
    }
}
