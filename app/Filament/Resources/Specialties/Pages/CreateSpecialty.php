<?php

namespace App\Filament\Resources\Specialties\Pages;

use App\Filament\Resources\Specialties\SpecialtyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialty extends CreateRecord
{
    protected static string $resource = SpecialtyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
