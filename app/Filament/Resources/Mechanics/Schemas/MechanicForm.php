<?php

namespace App\Filament\Resources\Mechanics\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MechanicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('specialty_id')
                    ->relationship('specialty', 'name')
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
