<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Services\TripService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListTrips extends ListRecords
{
    protected static string $resource = TripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('planCaravan')
                ->label('Planificar Caravana')
                ->icon('heroicon-o-truck')
                ->color('info')
                ->schema([
                    Select::make('mine_id')
                        ->label('Mina Destino')
                        ->relationship('mine', 'name', fn (Builder $query) => $query->where('is_active', true))
                        ->required(),
                    DateTimePicker::make('departure_date')
                        ->label('Fecha de salida')
                        ->default(now())
                        ->required(),
                    DateTimePicker::make('return_date')
                        ->label('Fecha estimada de retorno'),
                    TextInput::make('origin')
                        ->label('Origen')
                        ->default('Base Central')
                        ->required(),

                    Repeater::make('vehicles')
                        ->label('Vehículos y Choferes')
                        ->schema([
                            Select::make('vehicle_id')
                                ->label('Vehículo')
                                ->options(Vehicle::all()->pluck('plate', 'id'))
                                ->searchable()
                                ->live()
                                ->required(),

                            Select::make('outbound_driver_id')
                                ->label('Chofer Ida')
                                ->options(function (callable $get) {
                                    $vehicleId = $get('vehicle_id');
                                    if (! $vehicleId) {
                                        return [];
                                    }
                                    $vehicle = Vehicle::find($vehicleId);
                                    $type = $vehicle?->brandModel?->vehicle_type;

                                    return Driver::active()
                                        ->whereHas('licenses', fn ($q) => $q
                                            ->where('status', 'active')
                                            ->where('expiry_date', '>=', now())
                                            ->whereHas('category', fn ($c) => $c->where('vehicle_type', $type))
                                        )
                                        ->get()
                                        ->pluck('full_name', 'id');
                                })
                                ->searchable()
                                ->required(),

                            Select::make('return_driver_id')
                                ->label('Chofer Vuelta')
                                ->options(function (callable $get) {
                                    $vehicleId = $get('vehicle_id');
                                    if (! $vehicleId) {
                                        return [];
                                    }
                                    $vehicle = Vehicle::find($vehicleId);
                                    $type = $vehicle?->brandModel?->vehicle_type;

                                    return Driver::active()
                                        ->whereHas('licenses', fn ($q) => $q
                                            ->where('status', 'active')
                                            ->where('expiry_date', '>=', now())
                                            ->whereHas('category', fn ($c) => $c->where('vehicle_type', $type))
                                        )
                                        ->get()
                                        ->pluck('full_name', 'id');
                                })
                                ->searchable()
                                ->required(),
                        ])
                        ->columns(3)
                        ->minItems(1)
                        ->addActionLabel('Añadir otro vehículo'),
                ])
                ->action(function (array $data) {
                    try {
                        app(TripService::class)->createBatch($data);

                        Notification::make()
                            ->title('Caravana planificada con éxito')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error al planificar caravana')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make()
                ->icon(Heroicon::SquaresPlus),
        ];
    }
}
