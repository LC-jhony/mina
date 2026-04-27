<?php

namespace App\Filament\Resources\Trips\Tables;

use App\Enum\TripStatus;
use App\Models\Trip;
use App\Services\TripService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->paginated([8, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(8)
            ->defaultGroup('cluster')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->badge()
                    ->sortable(),
                TextColumn::make('vehicle.plate')
                    ->label('Vehículo')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color(fn ($record) => $record->vehicle->status->getColor()),
                TextColumn::make('mine.name')
                    ->label('Mina')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('departure_date')
                    ->label('Fecha salida')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label('Fecha retorno')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('En ruta...')
                    ->sortable(),
                TextColumn::make('outboundDrivers.driver.full_name')
                    ->label('Choferes Ida')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(2),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TripStatus::class),
                SelectFilter::make('mine_id')
                    ->label('Mina')
                    ->relationship('mine', 'name'),
                Filter::make('departure_date')
                    ->form([
                        DatePicker::make('from')->label('Desde'),
                        DatePicker::make('to')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('departure_date', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('departure_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                Action::make('start')
                    ->label('Iniciar viaje')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Trip $record) {
                        try {
                            app(TripService::class)->startTrip($record);
                            Notification::make()
                                ->title('Viaje iniciado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Trip $record) => $record->status === TripStatus::SCHEDULED),

                Action::make('complete')
                    ->label('Completar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('final_mileage')
                            ->label('Kilometraje Final')
                            ->numeric()
                            ->required()
                            ->default(fn (Trip $record) => $record->vehicle->mileage),
                        Textarea::make('observations')
                            ->label('Observaciones Finales'),
                    ])
                    ->action(function (Trip $record, array $data) {
                        try {
                            app(TripService::class)->completeTrip($record, (int) $data['final_mileage'], $data['observations']);
                            Notification::make()
                                ->title('Viaje completado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Trip $record) => $record->status === TripStatus::IN_PROGRESS),

                Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Trip $record) {
                        try {
                            app(TripService::class)->cancelTrip($record);
                            Notification::make()
                                ->title('Viaje cancelado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Trip $record) => $record->status === TripStatus::SCHEDULED),

                EditAction::make()
                    ->visible(fn (Trip $record) => $record->status === TripStatus::SCHEDULED),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
