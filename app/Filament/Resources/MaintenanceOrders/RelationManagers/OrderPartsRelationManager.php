<?php

namespace App\Filament\Resources\MaintenanceOrders\RelationManagers;

use App\Models\SparePart;
use App\Services\MaintenanceService;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrderPartsRelationManager extends RelationManager
{
    protected static string $relationship = 'parts';

    protected static ?string $title = 'Repuestos Utilizados';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('spare_part_id')
                    ->label('Repuesto')
                    ->options(function () {
                        return SparePart::all()->mapWithKeys(fn ($p) => [
                            $p->id => "{$p->code} - {$p->name} (Stock: {$p->stock})",
                        ]);
                    })
                    ->searchable()
                    ->required()
                    ->live(),
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('sparePart.code')
                    ->label('Código')
                    ->fontFamily('mono'),
                TextColumn::make('sparePart.name')
                    ->label('Repuesto'),
                TextColumn::make('quantity')
                    ->label('Cant.')
                    ->badge(),
                TextColumn::make('unit_price')
                    ->label('Precio Unit. (Snap)')
                    ->money('PEN'),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('PEN')
                    ->weight('bold'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir Repuesto')
                    ->using(function (array $data): Model {
                        return app(MaintenanceService::class)->addPart(
                            $this->getOwnerRecord(),
                            (int) $data['spare_part_id'],
                            (int) $data['quantity']
                        );
                    })
                    ->visible(fn () => $this->getOwnerRecord()->isOpen()),
            ])
            ->actions([
                DeleteAction::make()
                    ->label('Quitar')
                    ->using(fn (Model $record) => app(MaintenanceService::class)->removePart($record))
                    ->visible(fn () => $this->getOwnerRecord()->isOpen()),
            ]);
    }
}
