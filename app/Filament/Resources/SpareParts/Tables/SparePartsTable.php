<?php

namespace App\Filament\Resources\SpareParts\Tables;

use App\Models\SparePart;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SparePartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->paginated([8, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(8)
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->fontFamily('mono')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('unit')
                    ->label('Unidad')
                    ->badge(),
                TextColumn::make('unit_price')
                    ->label('Precio Unit.')
                    ->money('PEN')
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->badge()
                    ->color(fn ($state) => $state <= 5 ? 'danger' : ($state <= 10 ? 'warning' : 'success'))
                    ->sortable(),
                IconColumn::make('is_low_stock')
                    ->label('Bajo Stock')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('')
                    ->color('danger'),
                TextColumn::make('maintenanceOrderParts_sum_quantity')
                    ->label('Total Consumido')
                    ->sum('maintenanceOrderParts', 'quantity')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                TernaryFilter::make('low_stock')
                    ->label('Solo stock bajo')
                    ->query(fn ($query) => $query->lowStock()),
            ])
            ->recordActions([
                Action::make('adjust_stock')
                    ->label('Ajustar Stock')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('warning')
                    ->form([
                        Select::make('type')
                            ->label('Tipo de ajuste')
                            ->options([
                                'add' => 'Entrada (Incrementar)',
                                'set' => 'Corrección (Establecer valor fijo)',
                            ])
                            ->required(),
                        TextInput::make('amount')
                            ->label('Cantidad / Nuevo valor')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (SparePart $record, array $data) {
                        if ($data['type'] === 'add') {
                            $record->increment('stock', (int) $data['amount']);
                        } else {
                            $record->update(['stock' => (int) $data['amount']]);
                        }

                        Notification::make()
                            ->title('Stock actualizado')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
