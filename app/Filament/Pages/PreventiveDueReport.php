<?php

namespace App\Filament\Pages;

use App\Models\MaintenanceOrder;
use App\Models\MaintenanceType;
use App\Models\Vehicle;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class PreventiveDueReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected string $view = 'filament.pages.preventive-due-report';

    protected static ?string $title = 'Reporte de Mantenimiento Preventivo';

    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';

    public function table(Table $table): Table
    {
        return $table
            ->query(Vehicle::query()->where('status', '!=', 'out_of_service'))
            ->columns([
                TextColumn::make('plate')
                    ->label('Vehículo')
                    ->badge()
                    ->searchable(),
                TextColumn::make('mileage')
                    ->label('KM Actual')
                    ->numeric(),
                TextColumn::make('due_maintenance')
                    ->label('Mantenimientos Vencidos')
                    ->state(fn (Vehicle $record) => $this->getOverdueMaintenances($record))
                    ->listWithLineBreaks()
                    ->html()
                    ->color('danger'),
            ]);
    }

    protected function getOverdueMaintenances(Vehicle $vehicle): array
    {
        $overdue = [];
        $types = MaintenanceType::whereNotNull('interval_km')->orWhereNotNull('interval_days')->get();

        foreach ($types as $type) {
            $lastOrder = MaintenanceOrder::where('vehicle_id', $vehicle->id)
                ->where('maintenance_type_id', $type->id)
                ->where('status', 'completed')
                ->latest('end_date')
                ->first();

            $isOverdue = false;
            $reason = '';

            if ($type->interval_km) {
                $baseKm = $lastOrder?->mileage_at_service ?? 0;
                $dueKm = $baseKm + $type->interval_km;
                if ($vehicle->mileage >= $dueKm) {
                    $isOverdue = true;
                    $reason .= 'KM: '.($vehicle->mileage - $dueKm).' exceso. ';
                }
            }

            if ($type->interval_days) {
                $baseDate = $lastOrder?->end_date ?? $vehicle->created_at;
                $daysSince = now()->diffInDays($baseDate);
                if ($daysSince >= $type->interval_days) {
                    $isOverdue = true;
                    $reason .= 'Días: '.($daysSince - $type->interval_days).' exceso.';
                }
            }

            if ($isOverdue) {
                $overdue[] = "<strong>{$type->name}</strong><br><small>{$reason}</small>";
            }
        }

        return $overdue;
    }
}
