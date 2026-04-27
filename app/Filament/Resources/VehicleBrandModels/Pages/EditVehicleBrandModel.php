<?php

namespace App\Filament\Resources\VehicleBrandModels\Pages;

use App\Filament\Resources\VehicleBrandModels\VehicleBrandModelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicleBrandModel extends EditRecord
{
    protected static string $resource = VehicleBrandModelResource::class;

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
