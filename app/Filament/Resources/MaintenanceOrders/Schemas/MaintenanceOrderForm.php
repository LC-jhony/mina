<?php

namespace App\Filament\Resources\MaintenanceOrders\Schemas;

use App\Enum\InspectionStatus;
use App\Enum\VehicleStatus;
use App\Models\Mechanic;
use App\Models\Trip;
use App\Models\Vehicle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class MaintenanceOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Vehículo')
                    ->columns(2)
                    ->components([
                        Select::make('vehicle_id')
                            ->label('Vehículo')
                            ->relationship('vehicle', 'plate', function (Builder $query) {
                                // En creación, solo vehículos disponibles o en viaje
                                // Pero si es edición, debe permitir el actual
                                return $query->whereIn('status', [VehicleStatus::Available, VehicleStatus::OnTrip, VehicleStatus::InMaintenance]);
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Select::make('trip_id')
                            ->label('Viaje asociado')
                            ->options(function (callable $get) {
                                $vehicleId = $get('vehicle_id');
                                if (! $vehicleId) {
                                    return [];
                                }

                                return Trip::where('vehicle_id', $vehicleId)
                                    ->where('status', 'in_progress')
                                    ->get()
                                    ->pluck('id', 'id')
                                    ->mapWithKeys(fn($id) => [$id => "Viaje #$id"]);
                            })
                            ->placeholder('Ninguno (opcional)')
                            ->hint('Solo viajes en progreso')
                            ->searchable(),
                    ]),

                Section::make('Asignación')
                    ->columns(2)
                    ->components([
                        Select::make('mechanic_id')
                            ->label('Mecánico')
                            ->options(function () {
                                return Mechanic::where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(fn($m) => [
                                        $id = $m->id => $m->full_name . ($m->hasActiveOrder() ? ' (OCUPADO)' : ' (LIBRE)'),
                                    ]);
                            })
                            ->searchable()
                            ->required(),
                        Select::make('maintenance_type_id')
                            ->label('Tipo de Mantenimiento')
                            ->relationship('maintenanceType', 'name')
                            ->required(),
                    ]),

                Section::make('Inspección de Componentes')
                    ->description('Registre el estado de los componentes críticos (Aceite, Filtros, Frenos, etc.)')
                    ->components([
                        Repeater::make('inspections')
                            ->relationship('inspections')
                            ->label('Checklist de Inspección')
                            ->columns(4)
                            ->components([
                                Select::make('category')
                                    ->label('Categoría')
                                    ->options([
                                        'fluids' => 'Fluidos (Aceite/Refrigerante)',
                                        'filters' => 'Filtros',
                                        'brakes' => 'Frenos',
                                        'tires' => 'Neumáticos',
                                        'other' => 'Otros',
                                    ])
                                    ->required(),
                                TextInput::make('item_label')
                                    ->label('Componente')
                                    ->placeholder('Ej: Aceite Motor, Pastilla DI')
                                    ->required(),
                                TextInput::make('value')
                                    ->label('Medición/Valor')
                                    ->placeholder('Ej: 80%, Nuevo, 5mm'),
                                Select::make('status')
                                    ->label('Estado')
                                    ->options(InspectionStatus::class)
                                    ->required(),
                                Textarea::make('notes')
                                    ->label('Observaciones')
                                    ->columnSpanFull()
                                    ->rows(1),
                            ])
                            ->defaultItems(3)
                            ->addActionLabel('Añadir punto de inspección')
                            ->reorderableWithButtons(),
                    ]),

                Section::make('Detalles')
                    ->columns(2)
                    ->components([
                        DateTimePicker::make('start_date')
                            ->label('Fecha inicio')
                            ->default(now())
                            ->required(),
                        TextInput::make('mileage_at_service')
                            ->label('Kilometraje al entrar')
                            ->numeric()
                            ->required()
                            ->default(fn($get) => Vehicle::find($get('vehicle_id'))?->mileage),
                        Textarea::make('description')
                            ->label('Descripción / Fallas reportadas')
                            ->columnSpanFull(),
                    ]),

                Section::make('Documentos')
                    ->columns(1)
                    ->components([
                        FileUpload::make('attachment_path')
                            ->label('Boleta/Recibo del mantenimiento')
                            ->directory('maintenance-attachments')
                            ->acceptedFileTypes(['pdf', 'jpg', 'jpeg', 'png'])
                            ->downloadable()
                            ->hint('PDF, JPG o PNG'),
                    ]),
            ]);
    }
}
