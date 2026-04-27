<?php

namespace App\Filament\Resources\VehicleBrandModels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VehicleBrandModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand')
                    ->label('Marca')
                    ->required(),
                TextInput::make('model')
                    ->label('Modelo')
                    ->required(),
                TextInput::make('vehicle_type')
                    ->label('Tipo de vehículo')
                    ->required(),
                TextInput::make('passenger_capacity')
                    ->label('Capacidad de pasajeros')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }
}
