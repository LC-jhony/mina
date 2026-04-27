<?php

namespace App\Filament\Pages;

use App\Models\Vehicle;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ReportPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.report-page';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Reportes';

    protected static ?string $title = 'Reportes';

    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';

    public function table(Table $table): Table
    {
        // En esta página usaremos widgets o tablas personalizadas dentro de la vista
        // para manejar múltiples reportes, ya que HasTable suele vincularse a una sola.
        return $table->query(Vehicle::query())->columns([]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Reportes Operacionales')
                    ->tabs([
                        Tab::make('Costos por Vehículo')
                            ->icon('heroicon-o-truck')
                            ->components([
                                // Aquí podríamos renderizar un widget o vista personalizada
                                View::make('filament.reports.cost-by-vehicle'),
                            ]),
                        Tab::make('Carga de Trabajo')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->components([
                                View::make('filament.reports.mechanic-workload'),
                            ]),
                        Tab::make('Consumo Repuestos')
                            ->icon('heroicon-o-cog')
                            ->components([
                                View::make('filament.reports.parts-consumption'),
                            ]),
                        Tab::make('Resumen Viajes')
                            ->icon('heroicon-o-map')
                            ->components([
                                View::make('filament.reports.trip-summary'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
