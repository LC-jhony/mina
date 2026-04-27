<?php

namespace App\Filament\Resources\Mechanics\Pages;

use App\Filament\Resources\Mechanics\MechanicResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMechanic extends CreateRecord
{
    protected static string $resource = MechanicResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
