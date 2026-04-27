<?php

namespace App\Filament\Resources\Mines\Pages;

use App\Filament\Resources\Mines\MineResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMine extends CreateRecord
{
    protected static string $resource = MineResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
