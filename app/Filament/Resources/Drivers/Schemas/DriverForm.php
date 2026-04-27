<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('last_name')
                    ->label('Apellido')
                    ->required(),
                TextInput::make('dni')
                    ->label('DNI')
                    ->required(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->default(null),
                DatePicker::make('birth_date')
                    ->label('Fecha de nacimiento')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->required(),
            ]);
    }
}
