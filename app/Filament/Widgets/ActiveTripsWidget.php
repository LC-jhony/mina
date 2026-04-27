<?php

namespace App\Filament\Widgets;

use App\Enum\TripStatus;
use App\Filament\Resources\Trips\TripResource;
use App\Models\Trip;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActiveTripsWidget extends BaseWidget
{
    protected static ?string $heading = 'Viajes en Curso';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Trip::query()
                    ->where('status', TripStatus::IN_PROGRESS)
                    ->with(['vehicle', 'mine'])
                    ->latest('departure_date')
            )
            ->columns([
                TextColumn::make('vehicle.plate')
                    ->label('Vehículo')
                    ->badge(),
                TextColumn::make('mine.name')
                    ->label('Mina'),
                TextColumn::make('departure_date')
                    ->label('Salida')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('elapsed_time')
                    ->label('Tiempo en ruta')
                    ->state(fn (Trip $record) => $record->departure_date->diffForHumans(now(), true)),
            ])
            ->actions([
                Action::make('view')
                    ->label('Ver detalle')
                    ->url(fn (Trip $record): string => TripResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
