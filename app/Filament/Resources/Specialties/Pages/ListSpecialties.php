<?php

namespace App\Filament\Resources\Specialties\Pages;

use App\Filament\Resources\Specialties\SpecialtyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListSpecialties extends ListRecords
{
    protected static string $resource = SpecialtyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon(Heroicon::SquaresPlus),
        ];
    }
}
