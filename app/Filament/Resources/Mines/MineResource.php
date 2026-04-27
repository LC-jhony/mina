<?php

namespace App\Filament\Resources\Mines;

use App\Filament\Resources\Mines\Pages\CreateMine;
use App\Filament\Resources\Mines\Pages\EditMine;
use App\Filament\Resources\Mines\Pages\ListMines;
use App\Filament\Resources\Mines\Schemas\MineForm;
use App\Filament\Resources\Mines\Tables\MinesTable;
use App\Models\Mine;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MineResource extends Resource
{
    protected static ?string $model = Mine::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Mina';

    protected static ?string $pluralModelLabel = 'Minas';

    protected static string|UnitEnum|null $navigationGroup = 'Operaciones';

    public static function form(Schema $schema): Schema
    {
        return MineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MinesTable::configure($table);
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
            'index' => ListMines::route('/'),
            'create' => CreateMine::route('/create'),
            'edit' => EditMine::route('/{record}/edit'),
        ];
    }
}
