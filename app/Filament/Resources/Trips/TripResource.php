<?php

namespace App\Filament\Resources\Trips;

use App\Filament\Resources\Trips\Pages\CreateTrip;
use App\Filament\Resources\Trips\Pages\EditTrip;
use App\Filament\Resources\Trips\Pages\ListTrips;
use App\Filament\Resources\Trips\RelationManagers\MaintenanceOrdersRelationManager;
use App\Filament\Resources\Trips\RelationManagers\TripDriversRelationManager;
use App\Filament\Resources\Trips\Schemas\TripForm;
use App\Filament\Resources\Trips\Tables\TripsTable;
use App\Models\Trip;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $recordTitleAttribute = 'origin';

    protected static ?string $modelLabel = 'Viaje';

    protected static ?string $pluralModelLabel = 'Viajes';

    protected static string|UnitEnum|null $navigationGroup = 'Operaciones';

    public static function form(Schema $schema): Schema
    {
        return TripForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TripDriversRelationManager::class,
            MaintenanceOrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrips::route('/'),
            'create' => CreateTrip::route('/create'),
            'edit' => EditTrip::route('/{record}/edit'),
        ];
    }
}
