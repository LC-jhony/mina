<?php

namespace App\Filament\Resources\Mines\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('region')
                    ->label('Región')
                    ->required(),
                TextInput::make('company')
                    ->label('Empresa')
                    ->required(),
                TextInput::make('address')
                    ->label('Dirección')
                    ->default(null),
                TextInput::make('latitude')
                    ->label('Latitud')
                    ->numeric()
                    ->default(null),
                TextInput::make('longitude')
                    ->label('Longitud')
                    ->numeric()
                    ->default(null),
                Toggle::make('is_active')
                    ->label('Activa')
                    ->required(),
            ]);
    }
}
