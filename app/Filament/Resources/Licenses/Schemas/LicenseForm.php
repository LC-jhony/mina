<?php

namespace App\Filament\Resources\Licenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LicenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('driver_id')
                    ->label('Conductor')
                    ->relationship('driver', 'fullName')
                    ->required(),
                Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'code')
                    ->required(),
                TextInput::make('license_number')
                    ->label('Número de licencia')
                    ->required(),
                DatePicker::make('issue_date')
                    ->label('Fecha de emisión')
                    ->required(),
                DatePicker::make('expiry_date')
                    ->label('Fecha de vencimiento')
                    ->required(),
                Select::make('status')
                    ->label('Estado')
                    ->options(['active' => 'Activa', 'expired' => 'Vencida', 'suspended' => 'Suspendida'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
