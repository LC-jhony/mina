<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SpareParts\SparePartResource;
use App\Models\SparePart;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockWidget extends BaseWidget
{
    protected static ?string $heading = 'Repuestos con Bajo Stock';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SparePart::query()->lowStock()->orderBy('stock')
            )
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->fontFamily('mono'),
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('stock')
                    ->label('Stock Actual')
                    ->badge()
                    ->color('danger'),
                TextColumn::make('unit')
                    ->label('Unidad'),
            ])
            ->actions([
                Action::make('adjust')
                    ->label('Ajustar')
                    ->url(fn (SparePart $record): string => SparePartResource::getUrl('index', ['tableFilters[low_stock][value]' => 1])),
            ]);
    }
}
