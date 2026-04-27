<?php

namespace App\Filament\Resources\Licenses\Pages;

use App\Filament\Resources\Licenses\LicenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListLicenses extends ListRecords
{
    protected static string $resource = LicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon(Heroicon::SquaresPlus),
        ];
    }
}
