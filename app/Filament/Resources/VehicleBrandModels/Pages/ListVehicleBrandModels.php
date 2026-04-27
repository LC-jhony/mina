<?php

namespace App\Filament\Resources\VehicleBrandModels\Pages;

use App\Filament\Resources\VehicleBrandModels\VehicleBrandModelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListVehicleBrandModels extends ListRecords
{
    protected static string $resource = VehicleBrandModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon(Heroicon::SquaresPlus),
        ];
    }
}
