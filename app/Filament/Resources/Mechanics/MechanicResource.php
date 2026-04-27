<?php

namespace App\Filament\Resources\Mechanics;

use App\Filament\Resources\Mechanics\Pages\CreateMechanic;
use App\Filament\Resources\Mechanics\Pages\EditMechanic;
use App\Filament\Resources\Mechanics\Pages\ListMechanics;
use App\Filament\Resources\Mechanics\Schemas\MechanicForm;
use App\Filament\Resources\Mechanics\Tables\MechanicsTable;
use App\Models\Mechanic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MechanicResource extends Resource
{
    protected static ?string $model = Mechanic::class;

    protected static ?string $modelLabel = 'Mecánico';

    protected static ?string $pluralModelLabel = 'Mecánicos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Mantenimiento';

    public static function form(Schema $schema): Schema
    {
        return MechanicForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MechanicsTable::configure($table);
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
            'index' => ListMechanics::route('/'),
            'create' => CreateMechanic::route('/create'),
            'edit' => EditMechanic::route('/{record}/edit'),
        ];
    }
}
