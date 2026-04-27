<?php

namespace App\Filament\Resources\MaintenanceOrders;

use App\Filament\Resources\MaintenanceOrders\Pages\CreateMaintenanceOrder;
use App\Filament\Resources\MaintenanceOrders\Pages\EditMaintenanceOrder;
use App\Filament\Resources\MaintenanceOrders\Pages\ListMaintenanceOrders;
use App\Filament\Resources\MaintenanceOrders\RelationManagers\OrderPartsRelationManager;
use App\Filament\Resources\MaintenanceOrders\Schemas\MaintenanceOrderForm;
use App\Filament\Resources\MaintenanceOrders\Tables\MaintenanceOrdersTable;
use App\Models\MaintenanceOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MaintenanceOrderResource extends Resource
{
    protected static ?string $model = MaintenanceOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrench;

    protected static string|UnitEnum|null $navigationGroup = 'Mantenimiento';

    protected static ?string $modelLabel = 'Orden de mantenimiento';

    protected static ?string $pluralModelLabel = 'Órdenes de mantenimiento';

    public static function form(Schema $schema): Schema
    {
        return MaintenanceOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OrderPartsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaintenanceOrders::route('/'),
            'create' => CreateMaintenanceOrder::route('/create'),
            'edit' => EditMaintenanceOrder::route('/{record}/edit'),
        ];
    }
}
