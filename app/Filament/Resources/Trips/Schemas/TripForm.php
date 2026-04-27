<?php

namespace App\Filament\Resources\Trips\Schemas;

use App\Models\Driver;
use App\Models\Vehicle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Vehículo y destino')
                        ->icon('heroicon-o-truck')
                        ->columns(2)
                        ->components([
                            Select::make('vehicle_id')
                                ->label('Vehículo')
                                ->relationship('vehicle', 'plate', fn (Builder $query) => $query->where('status', 'available'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->required()
                                ->hint(fn ($get) => $get('vehicle_id') ? 'Tipo: '.Vehicle::find($get('vehicle_id'))?->brandModel->vehicle_type : null),
                            Select::make('mine_id')
                                ->label('Mina')
                                ->relationship('mine', 'name', fn (Builder $query) => $query->where('is_active', true))
                                ->searchable()
                                ->preload()
                                ->required(),
                            DateTimePicker::make('departure_date')
                                ->label('Fecha de salida')
                                ->default(now())
                                ->native(false)
                                ->required(),
                            TextInput::make('origin')
                                ->label('Origen')
                                ->default('Base Central')
                                ->required(),
                            TextInput::make('cluster')
                                ->label('Clúster / Caravana')
                                ->placeholder('Ej: CARAVAN-001')
                                ->nullable(),
                            Textarea::make('observations')
                                ->label('Observaciones')
                                ->columnSpanFull(),
                        ]),

                    Step::make('Choferes de ida')
                        ->icon('heroicon-o-arrow-right')
                        ->description('Seleccione los 2 conductores para el tramo de ida.')
                        ->columns(2)
                        ->components([
                            self::getDriverSelect('outbound_driver_1_id', 'Chofer principal ida'),
                            self::getDriverSelect('outbound_driver_2_id', 'Chofer secundario ida'),
                        ]),

                    Step::make('Choferes de vuelta')
                        ->icon('heroicon-o-arrow-left')
                        ->description('Seleccione los 2 conductores para el tramo de vuelta.')
                        ->columns(2)
                        ->components([
                            self::getDriverSelect('return_driver_1_id', 'Chofer principal vuelta'),
                            self::getDriverSelect('return_driver_2_id', 'Chofer secundario vuelta'),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    protected static function getDriverSelect(string $name, string $label): Select
    {
        return Select::make($name)
            ->label($label)
            ->options(function (callable $get) {
                $vehicleId = $get('vehicle_id');
                if (! $vehicleId) {
                    return [];
                }

                $vehicle = Vehicle::find($vehicleId);
                if (! $vehicle) {
                    return [];
                }

                $vehicleType = $vehicle->brandModel->vehicle_type;

                return Driver::active()
                    ->whereHas('licenses', function ($query) use ($vehicleType) {
                        $query->whereHas('category', fn ($q) => $q->where('vehicle_type', $vehicleType))
                            ->where('status', 'active')
                            ->where('expiry_date', '>=', now());
                    })
                    ->get()
                    ->mapWithKeys(fn ($driver) => [
                        $driver->id => "{$driver->full_name} ({$driver->dni})",
                    ]);
            })
            ->searchable()
            ->required()
            ->live();
    }
}
