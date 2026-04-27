# Plan de Implementación - Sistema de Mantenimiento de Vehículos

## Estado Actual

| Función | Estado |
|---------|--------|
| Un vehículo → varios mantenimientos | ✅ Ya existe |
| Un mantenimiento → varios repuestos | ✅ Ya existe |
| Valorización total (costo por orden) | ✅ Ya existe (`total_cost`) |
| **Adjuntar boleta/recibo** | ❌ Falta |
| Historial de mantenimientos en Vehicle | ❌ Falta |

---

## Entidades Actuales

### Modelos
- `Vehicle` - Vehículos
- `MaintenanceOrder` - Órdenes de mantenimiento
- `MaintenanceOrderPart` - Repuestos utilizados
- `MaintenanceType` - Tipos de mantenimiento
- `Mechanic` - Mecánicos
- `SparePart` - Repuestos

### Tabla `maintenance_orders`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| vehicle_id | bigint | FK a vehicles |
| trip_id | bigint nullable | FK a trips |
| mechanic_id | bigint | FK a mechanics |
| maintenance_type_id | bigint | FK a maintenance_types |
| start_date | datetime | Fecha de inicio |
| end_date | datetime nullable | Fecha de cierre |
| mileage_at_service | int unsigned | Kilometraje al entrar |
| description | text nullable | Descripción/fallas |
| status | enum | pending, in_progress, completed, cancelled |
| total_cost | decimal(10,2) | Costo total |

---

## Tareas Implementadas

### 1. ✅ Agregar campos para boleta/recibo

**Migración**: `database/migrations/2026_04_24_000001_add_attachment_to_maintenance_orders_table.php`

```php
$table->string('attachment_path')->nullable();
$table->string('attachment_name')->nullable();
```

### 2. ✅ Actualizar Modelo `MaintenanceOrder.php`

Agregado a `$fillable`:
- `attachment_path`
- `attachment_name`

### 3. ✅ Actualizar Formulario `MaintenanceOrderForm.php`

Agregado campo de upload:
```php
FileUpload::make('attachment_path')
    ->label('Boleta/Recibo del mantenimiento')
    ->directory('maintenance-attachments')
    ->acceptedTypes(['pdf', 'jpg', 'jpeg', 'png'])
    ->downloadable()
```

### 4. ✅ Actualizar Tabla/Detalle

Mostrar icono de adjunto en `MaintenanceOrdersTable.php`:
```php
IconColumn::make('attachment_path')
    ->icon('heroicon-o-paper-clip')
    ->url(fn ($record) => $record->attachment_path ? asset('storage/'.$record->attachment_path) : null)
```

### 5. ✅ Historial de mantenimientos en Vehicle

Creado `MaintenanceOrdersRelationManager` en `app/Filament/Resources/Vehicles/RelationManagers/`

---

## Estado: ✅ COMPLETADO

- Todos los cambios implementados
- Migración ejecutada
- Tests pasan (1 failure preexistente)