<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use App\Services\TripService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrip extends EditRecord
{
    protected static string $resource = TripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $outbound1 = $this->record->tripDrivers()
            ->where('leg', 'outbound')
            ->where('position', 1)
            ->first();

        $outbound2 = $this->record->tripDrivers()
            ->where('leg', 'outbound')
            ->where('position', 2)
            ->first();

        $return1 = $this->record->tripDrivers()
            ->where('leg', 'return')
            ->where('position', 1)
            ->first();

        $return2 = $this->record->tripDrivers()
            ->where('leg', 'return')
            ->where('position', 2)
            ->first();

        $data['outbound_driver_1_id'] = $outbound1?->driver_id;
        $data['outbound_driver_2_id'] = $outbound2?->driver_id;
        $data['return_driver_1_id'] = $return1?->driver_id;
        $data['return_driver_2_id'] = $return2?->driver_id;

        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->form->getRawState();

        app(TripService::class)->syncDrivers($this->record, [
            'outbound_driver_1_id' => $data['outbound_driver_1_id'],
            'outbound_driver_2_id' => $data['outbound_driver_2_id'],
            'return_driver_1_id' => $data['return_driver_1_id'],
            'return_driver_2_id' => $data['return_driver_2_id'],
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
