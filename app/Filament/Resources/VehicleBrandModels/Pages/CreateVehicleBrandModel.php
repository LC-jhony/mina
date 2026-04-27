<?php

namespace App\Filament\Resources\VehicleBrandModels\Pages;

use App\Filament\Resources\VehicleBrandModels\VehicleBrandModelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicleBrandModel extends CreateRecord
{
    protected static string $resource = VehicleBrandModelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
