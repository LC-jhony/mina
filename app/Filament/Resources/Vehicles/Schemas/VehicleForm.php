<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Enum\VehicleStatus;
use App\Models\VehicleBrandModel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('brand_model_id')
                    ->label('Marca/Modelo')
                    ->options(VehicleBrandModel::all()->pluck('brand', 'id'))
                    ->required()
                    ->native(false),
                TextInput::make('plate')
                    ->label('Placa')
                    ->required(),
                TextInput::make('year')
                    ->label('Año')
                    ->required()
                    ->numeric(),
                TextInput::make('mileage')
                    ->label('Kilometraje')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->label('Estado')
                    ->options(VehicleStatus::class)
                    ->default('available')
                    ->required(),
            ]);
    }
}
