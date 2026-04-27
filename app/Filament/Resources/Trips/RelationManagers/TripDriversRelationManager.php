<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use App\Enum\TripStatus;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripDriversRelationManager extends RelationManager
{
    protected static string $relationship = 'tripDrivers';

    protected static ?string $title = 'Conductores Asignados';

    protected static ?string $modelLabel = 'Asignación';

    protected static ?string $pluralModelLabel = 'Asignaciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('driver_id')
                    ->relationship('driver', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('leg')
                    ->options([
                        'outbound' => 'Ida',
                        'return' => 'Vuelta',
                    ])
                    ->required(),
                Select::make('position')
                    ->options([
                        1 => 'Principal',
                        2 => 'Secundario',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('driver_id')
            ->columns([
                TextColumn::make('leg')
                    ->label('Tramo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'outbound' => 'info',
                        'return' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'outbound' => 'Ida',
                        'return' => 'Vuelta',
                    }),
                TextColumn::make('position')
                    ->label('Posición')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => 'Principal',
                        2 => 'Secundario',
                    }),
                TextColumn::make('driver.full_name')
                    ->label('Conductor')
                    ->searchable(),
                TextColumn::make('driver.dni')
                    ->label('DNI'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Desactivado porque preferimos usar el Wizard del TripResource para cambios masivos
                // Pero dejamos el EditAction para ajustes finos si es necesario
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->status === TripStatus::SCHEDULED),
                DeleteAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->status === TripStatus::SCHEDULED),
            ]);
    }
}
