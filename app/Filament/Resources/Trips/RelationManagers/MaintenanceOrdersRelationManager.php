<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use App\Filament\Resources\MaintenanceOrders\Tables\MaintenanceOrdersTable;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MaintenanceOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceOrders';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return MaintenanceOrdersTable::configure($table);
    }
}
