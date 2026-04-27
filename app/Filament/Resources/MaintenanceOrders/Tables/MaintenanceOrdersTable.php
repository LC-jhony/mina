<?php

namespace App\Filament\Resources\MaintenanceOrders\Tables;

use App\Enum\MaintenanceStatus;
use App\Models\MaintenanceOrder;
use App\Services\MaintenanceService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MaintenanceOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->paginated([8, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(8)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->badge()
                    ->sortable(),
                TextColumn::make('vehicle.plate')
                    ->label('Vehículo')
                    ->badge()
                    ->color(fn ($record) => $record->vehicle->status->getColor())
                    ->searchable(),
                TextColumn::make('maintenanceType.name')
                    ->label('Tipo')
                    ->sortable(),
                TextColumn::make('mechanic.full_name')
                    ->label('Mecánico')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('total_cost')
                    ->label('Costo Total')
                    ->money('PEN')
                    ->weight('bold')
                    ->sortable(),
                IconColumn::make('attachment_path')
                    ->label('Adjunto')
                    ->icon('heroicon-o-paper-clip')
                    ->color('info')
                    ->size('sm')
                    ->url(fn ($record) => $record->attachment_path ? asset('storage/'.$record->attachment_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->attachment_path !== null),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(MaintenanceStatus::class),
                SelectFilter::make('vehicle_id')
                    ->relationship('vehicle', 'plate'),
                SelectFilter::make('mechanic_id')
                    ->relationship('mechanic', 'first_name'), // Simple relationship search
            ])
            ->recordActions([
                Action::make('close')
                    ->label('Cerrar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Textarea::make('notes')
                            ->label('Notas de cierre'),
                    ])
                    ->action(function (MaintenanceOrder $record, array $data) {
                        try {
                            app(MaintenanceService::class)->closeOrder($record, $data['notes']);
                            Notification::make()
                                ->title('Orden cerrada')
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
                    ->visible(fn (MaintenanceOrder $record) => $record->isOpen()),

                Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (MaintenanceOrder $record) {
                        try {
                            app(MaintenanceService::class)->cancelOrder($record);
                            Notification::make()
                                ->title('Orden cancelada')
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
                    ->visible(fn (MaintenanceOrder $record) => $record->status === MaintenanceStatus::PENDING),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
