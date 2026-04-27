<?php

namespace App\Filament\Resources\Vehicles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->paginated([8, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(8)
            ->columns([
                TextColumn::make('brandModel.fullName')
                    ->label('Marca/Modelo')
                    ->searchable(),
                TextColumn::make('plate')
                    ->label('Placa')
                    ->searchable(),
                TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mileage')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($status) => match ($status) {
                        'available' => 'success',
                        'on_trip' => 'warning',
                        'in_maintenance' => 'danger',
                        'out_of_service' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
