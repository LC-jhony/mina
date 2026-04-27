<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use App\Services\TripService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTrip extends CreateRecord
{
    protected static string $resource = TripResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(TripService::class)->createTrip($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
