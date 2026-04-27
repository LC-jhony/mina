<?php

namespace App\Filament\Resources\Licenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LicensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->paginated([8, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(8)
            ->columns([
                TextColumn::make('driver.full_name')
                    ->label('Conductor')
                    ->searchable(),
                TextColumn::make('category.code')
                    ->label('Categoría')
                    ->searchable(),
                TextColumn::make('license_number')
                    ->searchable(),
                TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->expiry_date->isPast() ? 'danger' : 'success'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($status) => match ($status) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'suspended' => 'warning',
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
