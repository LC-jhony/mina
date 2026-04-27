<?php

namespace App\Filament\Resources\Mines\Pages;

use App\Filament\Resources\Mines\MineResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMine extends EditRecord
{
    protected static string $resource = MineResource::class;

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
