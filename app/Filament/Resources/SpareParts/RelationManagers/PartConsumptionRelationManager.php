<?php

namespace App\Filament\Resources\SpareParts\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartConsumptionRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceOrderParts';

    protected static ?string $title = 'Historial de Consumo';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('maintenanceOrder.id')
                    ->label('ID Orden')
                    ->badge(),
                TextColumn::make('maintenanceOrder.vehicle.plate')
                    ->label('Vehículo')
                    ->badge(),
                TextColumn::make('maintenanceOrder.end_date')
                    ->label('Fecha')
                    ->dateTime('d/m/Y')
                    ->placeholder('Pendiente...'),
                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->badge(),
                TextColumn::make('unit_price')
                    ->label('Precio Snapshot')
                    ->money('PEN'),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('PEN')
                    ->weight('bold'),
            ])
            ->filters([
                //
            ])
            ->actions([])
            ->headerActions([]);
    }
}
