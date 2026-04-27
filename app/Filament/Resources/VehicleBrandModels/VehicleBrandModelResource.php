<?php

namespace App\Filament\Resources\VehicleBrandModels;

use App\Filament\Resources\VehicleBrandModels\Pages\CreateVehicleBrandModel;
use App\Filament\Resources\VehicleBrandModels\Pages\EditVehicleBrandModel;
use App\Filament\Resources\VehicleBrandModels\Pages\ListVehicleBrandModels;
use App\Filament\Resources\VehicleBrandModels\Schemas\VehicleBrandModelForm;
use App\Filament\Resources\VehicleBrandModels\Tables\VehicleBrandModelsTable;
use App\Models\VehicleBrandModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VehicleBrandModelResource extends Resource
{
    protected static ?string $model = VehicleBrandModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $modelLabel = 'Modelo de vehículo';

    protected static ?string $pluralModelLabel = 'Modelos de vehículos';

    protected static string|UnitEnum|null $navigationGroup = 'Operaciones';

    public static function form(Schema $schema): Schema
    {
        return VehicleBrandModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleBrandModelsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicleBrandModels::route('/'),
            'create' => CreateVehicleBrandModel::route('/create'),
            'edit' => EditVehicleBrandModel::route('/{record}/edit'),
        ];
    }
}
