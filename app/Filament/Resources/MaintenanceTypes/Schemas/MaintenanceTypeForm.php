<?php

namespace App\Filament\Resources\MaintenanceTypes\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MaintenanceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('interval_km')
                    ->numeric()
                    ->default(null),
                TextInput::make('interval_days')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
