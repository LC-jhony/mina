<?php

namespace App\Filament\Resources\Mines\Pages;

use App\Filament\Resources\Mines\MineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListMines extends ListRecords
{
    protected static string $resource = MineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon(Heroicon::SquaresPlus),
        ];
    }
}
