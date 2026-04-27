<?php

namespace App\Filament\Resources\Vehicles\RelationManagers;

use App\Filament\Resources\MaintenanceOrders\MaintenanceOrderResource;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaintenanceOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceOrders';

    protected static ?string $title = 'Historial de mantenimientos';

    public function table(Table $table): Table
    {
        return $table
            ->inverseRelationship('vehicle')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->badge(),
                TextColumn::make('maintenanceType.name')
                    ->label('Tipo'),
                TextColumn::make('start_date')
                    ->label('Fecha')
                    ->date('d/m/Y'),
                TextColumn::make('mileage_at_service')
                    ->label('Km'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('total_cost')
                    ->label('Costo')
                    ->money('PEN'),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Nueva orden')
                    ->slideOver()
                    // ->url(fn($ownerRecord): string => MaintenanceOrderResource::getUrl('create', ['initialVehicleId' => $ownerRecord->id ?? null]))
                    ->icon('heroicon-o-plus'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Ver')
                    ->url(fn($record): string => MaintenanceOrderResource::getUrl('view', ['record' => $record->id])),
            ]);
    }
}
