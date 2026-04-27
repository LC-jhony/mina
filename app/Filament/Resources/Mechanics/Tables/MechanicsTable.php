<?php

namespace App\Filament\Resources\Mechanics\Tables;

use App\Models\Mechanic;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MechanicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->paginated([8, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(8)
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('specialty.name')
                    ->label('Especialidad')
                    ->badge()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                IconColumn::make('is_available')
                    ->label('Disponible')
                    ->state(fn (Mechanic $record) => ! $record->hasActiveOrder())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                TextColumn::make('maintenance_orders_count')
                    ->label('OM Completadas')
                    ->counts('maintenanceOrders', fn ($query) => $query->where('status', 'completed'))
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado Activo'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
