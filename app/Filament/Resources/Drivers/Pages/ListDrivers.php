<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Filament\Resources\Drivers\DriverResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon(Heroicon::SquaresPlus),
        ];
    }
}
